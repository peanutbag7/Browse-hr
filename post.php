
<?php
$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

function timeAgo($dateString)
{
    if (empty($dateString)) {
        return "";
    }

    try {
        $date = new DateTime($dateString);
        $currentTime = new DateTime();

        $interval = $currentTime->diff($date);

        if ($interval->y >= 1) {
            return $interval->y . " y";
        } elseif ($interval->m >= 1) {
            return floor($interval->days / 7) . " w";
        } elseif ($interval->d >= 1) {
            return $interval->d . " d";
        } elseif ($interval->h >= 1) {
            return $interval->h . " h";
        } elseif ($interval->i >= 1) {
            return $interval->i . " m";
        } else {
            return "0m";
        }
    } catch (Exception $e) {
        return "";
    }
}

function replaceUrls($str) {
    $str = preg_replace(
        '/(https?:\/\/[^\s]+)/',
        '<a href="$1" target="_blank">$1</a>',
        $str
    );

    $str = preg_replace(
        '/@([^\s]+)/',
        '<a href="profile.php?username=$1" class="purple">@$1</a>',
        $str
    );

    return $str;
}

if (isset($_GET['id'])) {
    $post_id = $_GET['id'];
} else {
    echo "No post id was specified";
    exit();
}

($json = file_get_contents('https://webapi.highrise.game/posts/' . $post_id . '', false, stream_context_create($arrContextOptions))) or die("Error occured");

($data = json_decode($json, true)) or die("Post not found");

$post = $data["post"];

($json = file_get_contents('https://webapi.highrise.game/users/' . $post['author_id'] . '', false, stream_context_create($arrContextOptions))) or die('Error occured');

($data2 = json_decode($json, true)) or die('Invalid user id');

$userdata = $data2["user"];
?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" >
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="stylesheet" href="https://unpkg.com/bootstrap@4.5.0/dist/css/bootstrap.min.css">
		<link rel="stylesheet" href="https://unpkg.com/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
		<link rel="stylesheet" href="styles.css">
		<script src="https://code.jquery.com/jquery-3.1.0.min.js" integrity="sha256-cCueBR6CsyA4/9szpPfrX3s49M9vUU5BgtiJj06wt/s=" crossorigin="anonymous"></script>
		<title>Browse-HR project</title>
	</head>
	<body>
		<nav class="navbar navbar-expand-md position-absolute navbar-dark">
			<div class="container-fluid">
				<a href="index.php" id="logo">Browse-HR project</a>
			</div>
		</nav>

		<div class="container-fluid pt-3 pb-2 text-left text-light" style="margin-top:96px;">
			<div class="container-fluid">
				<div class="posts">
<?php
$post_content = "";

if ($post['type'] == "photo") {
    $post_text = replaceUrls($post['caption']);
    $post_image = $post['file_key'];
    $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<img src="https://d4v5j9dz6t9fz.cloudfront.net/$post_image" alt="" style="display:block;max-width:100%;" class="mb-4">	
HTML;
} elseif ($post['type'] == "video") {
    $post_text = replaceUrls($post['caption']);
    $post_video = $post['file_key'];
    $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<video style="display:block;max-width:100%;" class="mb-4" controls>
  <source src="src="https://d4v5j9dz6t9fz.cloudfront.net/$post_video">
  Your browser does not support the video tag.
</video>	
HTML;
} elseif ($post['type'] == "text") {
    $post_text = replaceUrls($post['body']['text']);
    $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
	
HTML;
}
?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	
<div class="row">
	
	<div class="col-6">	
		<h2 class="post-author"><a href="profile.php?id=<?php echo $post['author_id']; ?>" class="purple"><?php echo $userdata["username"]; ?></a></h2>
		
	</div>
	
	<div class="col-6 text-right">	
		<h4 class="float-right text-muted" title="<?php echo substr($post['created_at'], 0, 10); ?>"><?php echo timeAgo($post['created_at']); ?></h4>	
	</div>

</div>	
	
	
<?php echo $post_content; ?>


<br>	

	<h4 id="likes-and-comments"><i class="bi-heart purple"></i> <?php echo $post['num_likes']; ?>
	&nbsp;
	<a href="post?id=<?php echo $post['post_id']; ?>" class="text-light"><i class="bi-chat purple"></i> <?php echo $post['num_comments']; ?></a></h4>
	


	</div>
	</div>
	<h3 class="purple">Comments</h3>
<?php for ($i = 0; $i < 10; $i++) {
    if (isset($post['comments'][$i])) { ?>
		<div class="comment px-2 pt-2 mb-4">
		<h6><a href="profile.php?id=<?php echo $post['comments'][$i]['author_id']; ?>" class="purple my-2"><?php echo $post['comments'][$i]['author_name']; ?></a></h6>
		<p class="comment-content my-2"><?php echo $post['comments'][$i]['content']; ?></p>
		</div>
		<?php }
} ?>
						
					
					

					
				</div>
			</div>
		</div>


		<footer class="py-5">
			<div class="container">
				<div class="row">
					<div class="col-md-12 text-center">
						<p class="text-dark mb-0">Coded by @peanutbag using highrise Webapi</p>
					</div>
				</div>
			</div>
		</footer>
	</body>
</html>	
