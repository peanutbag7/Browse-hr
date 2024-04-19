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

function onlineTimeAgo($dateString)
{
    if (empty($dateString)) {
        return "Last online unknown";
    }

    try {
        $date = new DateTime($dateString);
        $currentTime = new DateTime();

        $interval = $currentTime->diff($date);

        if ($interval->y >= 1) {
            return "Last online ".$interval->y . " year" . ($interval->y > 1 ? "s" : "") . " ago";
        } elseif ($interval->m >= 1) {
            return "Last online ".floor($interval->days / 7) . " week" . (floor($interval->days / 7) > 1 ? "s" : "") . " ago";
        } elseif ($interval->d >= 1) {
            return "Last online ".$interval->d . " day" . ($interval->d > 1 ? "s" : "") . " ago";
        } elseif ($interval->h >= 1) {
            return "Last online ".$interval->h . " hour" . ($interval->h > 1 ? "s" : "") . " ago";
        } elseif ($interval->i >= 1) {
            return "Last online ".$interval->i . " minute" . ($interval->i > 1 ? "s" : "") . " ago";
        } else {
            return '<i class="online-circle bi-circle-fill"></i> Online';
        }
    } catch (Exception $e) {
        return "Last online unknown";
    }
}



function replaceUrls($str)
{
    $str = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank">$1</a>', $str);

    $str = preg_replace('/@([^\s]+)/', '<a href="profile.php?username=$1" class="purple">@$1</a>', $str);

    return $str;
}

if (isset($_GET['username'])) {
    $username = $_GET['username'];

    $json = file_get_contents('https://webapi.highrise.game/users?&username=' . $username . '&sort_order=asc&limit=1', false, stream_context_create($arrContextOptions));

    $data1 = json_decode($json, true);

    if (!isset($data1["users"][0]["user_id"])) {
        echo "User not found";
        exit();
    }
    $user_id = $data1["users"][0]["user_id"];
} elseif (isset($_GET['id'])) {
    $user_id = $_GET['id'];
} else {
    echo "No user specified";
    exit();
}

($json = file_get_contents('https://webapi.highrise.game/users/' . $user_id . '', false, stream_context_create($arrContextOptions))) or die('Error occured');

($data2 = json_decode($json, true)) or die('Invalid user id');

$userdata = $data2["user"];

if (isset($_GET['starts_after'])) {
    $starts_after = "&starts_after=" . $_GET['starts_after'];
} else {
    $starts_after = "";
}

if (isset($_GET['ends_before'])) {
    $ends_before = "&ends_before=" . $_GET['ends_before'];
} else {
    $ends_before = "";
}

$sort_order = "desc";

if (isset($_GET['sort_order'])) {
    $sort_order = $_GET['sort_order'];
}

if (isset($_GET['rooms'])) {
    $json = file_get_contents('https://webapi.highrise.game/rooms?limit=5' . $starts_after . $ends_before . '&sort_order=' . $sort_order . '&owner_id=' . $user_id . '', false, stream_context_create($arrContextOptions));

    $data3 = json_decode($json, true);

    $rooms = $data3["rooms"];
} else {
    $json = file_get_contents('https://webapi.highrise.game/posts?limit=5' . $starts_after . $ends_before . '&sort_order=' . $sort_order . '&author_id=' . $user_id . '', false, stream_context_create($arrContextOptions));

    $data3 = json_decode($json, true);


    $posts = $data3["posts"];
}
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
				<section class="jumbotron mb-0 text-light text-left" style="padding-top:96px;position:relative;margin-top:0px;">
			<h2 id="user-name"><?php echo $userdata['username']; ?></h2>
			<?php if (isset($userdata['crew']['name'])) { ?>
			<h6><?php echo $userdata['crew']['name']; ?></h6>
			<?php } ?>			
			<p id="user-bio" class="lead"><?php echo $userdata['bio']; ?></p>
			<p class="text-muted"><?php echo onlineTimeAgo($userdata['last_online_in']); ?></p>
			<div class="container-fluid">
			<?php if (isset($userdata['discord_id'])) { ?>
			<h1><a href="https://discordapp.com/users/<?php echo $userdata['discord_id']; ?>" class="purple"><i class="bi-discord"></i></a></h1>
			<?php } ?>
				<div class="row">
					<div class="col-md-3">
					<p><i class="bi-clock purple"></i> Joined on <strong><?php echo substr($userdata["joined_at"], 0, 10); ?></strong></p>
					</div>
					<div class="col-md-3">
					<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_following"]; ?></strong> following</p>
					</div>
							<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_followers"]; ?></strong> followers</p>

					</div>
					<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata["num_friends"]; ?></strong> friends</p>

					</div>
			
				</div>
			</div>	
		</section>
		
<div class="container-fluid pt-3 pb-2 text-left text-light">
		
<div class="row px-4">

<div class="col-9 py-3 mb-4 text-left text-light">
<ul class="nav nav-pills">
  <li class="nav-item">
    <a class="nav-link<?php if (!isset($_GET['rooms'])) {
        echo ' active';
    } ?>" href="profile.php?id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>">Posts</a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php if (isset($_GET['rooms'])) {
        echo ' active';
    } ?>" href="profile.php?rooms&id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>">Rooms</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="stats.php?id=<?php echo $user_id; ?>">Stats</a>
  </li>
</ul>					
</div>
		<div class="col-3 py-3 text-right">
		<?php if ($sort_order == "asc") { ?>
			<h4><a href="profile.php?id=<?php echo $user_id; ?>&sort_order=desc" class="text-muted"><i class="bi-sort-down"></i></a></h4>
			<?php } else { ?>
				<h4><a href="profile.php?id=<?php echo $user_id; ?>&sort_order=asc" class="text-muted"><i class="bi-sort-up"></i></a></h4>
		<?php } ?>
		</div>
		
</div>

<div class="container-fluid">
<?php if (isset($_GET['rooms'])) { ?>






				<div class="rooms">
<?php
$s_a = "";
$e_b = "";
for ($i = 0; $i < 5; $i++) {
    $post_content = "";
    if (isset($rooms[$i])) {

        $s_a = $rooms[$i]['room_id'];
        if ($i == 0) {
            $e_b = $rooms[$i]['room_id'];
        }

        $room_id = $rooms[$i]['room_id'];
        ?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	

	<h4 class="room-name"><a href="room.php?id=<?php echo $room_id; ?>" class="purple"><?php echo $rooms[$i]["disp_name"]; ?></a></h4>	
	<?php if (isset($rooms[$i]["description"])) { ?><p class="text-muted"><?php echo $rooms[$i]["description"]; ?></p><?php } ?>
	
	

<br>	


	</div>
	</div>
<?php
    }
}
?>
						
					
					
<div class="text-center">
<h3><?php if (isset($_GET['starts_after'])) { ?><a class="text-muted mr-4" href="profile.php?rooms&id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&ends_before=<?php echo $e_b; ?>">&laquo; Previous</a><?php } ?> 
<a class="text-muted" href="profile.php?rooms&id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&starts_after=<?php echo $s_a; ?>">Next &raquo;</a> </h3>
</div>
					
				</div>
	






	
<?php } else { ?>
				<div class="posts">
<?php
$s_a = "";
$e_b = "";
for ($i = 0; $i < 5; $i++) {
    $post_content = "";
    if (isset($posts[$i])) {

        $s_a = $posts[$i]['post_id'];
        if ($i == 0) {
            $e_b = $posts[$i]['post_id'];
        }

        $post_id = $posts[$i]['post_id'];
        if ($posts[$i]['type'] == "photo") {
            $post_text = replaceUrls($posts[$i]['caption']);
            $post_image = $posts[$i]['file_key'];
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<a href="post.php?id=$post_id">
<img src="https://d4v5j9dz6t9fz.cloudfront.net/$post_image" alt="" style="display:block;max-width:100%;" class="mb-4">
</a>
HTML;
        } elseif ($posts[$i]['type'] == "video") {
            $post_text = replaceUrls($posts[$i]['caption']);
            $post_video = $posts[$i]['file_key'];
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
<video style="display:block;max-width:100%;" class="mb-4" controls>
  <source src="src="https://d4v5j9dz6t9fz.cloudfront.net/$post_video">
  Your browser does not support the video tag.
</video>	
HTML;
        } elseif ($posts[$i]['type'] == "text") {
            $post_text = replaceUrls($posts[$i]['body']['text']);
            $post_content = <<<HTML
<h4 class="my-4 post-text">$post_text</h4>
	
HTML;
        }
        ?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	
<div class="row">
	
	<div class="col-6">	
	<h2 class="post-author"><?php echo $userdata["username"]; ?></h2>
		
	</div>
	
	<div class="col-6 text-right">	
		<h4 title="<?php echo substr($posts[$i]['created_at'], 0, 10); ?>">
			<a href="post.php?id=<?php echo $posts[$i]['post_id']; ?>" class="text-muted">
				<?php echo timeAgo($posts[$i]['created_at']); ?>
			</a>
		</h4>	
	</div>

</div>	
	
	
<?php echo $post_content; ?>


<br>	

	<h4 id="likes-and-comments"><i class="bi-heart purple"></i> <?php echo $posts[$i]['num_likes']; ?>
	&nbsp;
	<a href="post.php?id=<?php echo $posts[$i]['post_id']; ?>" class="text-light"><i class="bi-chat purple"></i> <?php echo $posts[$i]['num_comments']; ?></a></h4>
	


	</div>
	</div>
<?php
    }
}
?>
						
					
					
<div class="text-center">
<h3><?php if (isset($_GET['starts_after'])) { ?><a class="text-muted mr-4" href="profile.php?id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&ends_before=<?php echo $e_b; ?>">&laquo; Previous</a><?php } ?> 
<a class="text-muted" href="profile.php?id=<?php echo $user_id; ?>&sort_order=<?php echo $sort_order; ?>&starts_after=<?php echo $s_a; ?>">Next &raquo;</a> </h3>
</div>
					
				</div>
	<?php } ?>			
				
				
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
