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

function replaceUrls($str)
{
    $str = preg_replace(
        "/(https?:\/\/[^\s]+)/",
        '<a href="$1" target="_blank">$1</a>',
        $str
    );

    $str = preg_replace(
        "/@([^\s]+)/",
        '<a href="profile.php?username=$1" class="purple">@$1</a>',
        $str
    );

    return $str;
}

if (isset($_GET["username"])) {
    $username = $_GET["username"];

    $json = file_get_contents(
        "https://webapi.highrise.game/users?&username=" .
            $username .
            "&sort_order=asc&limit=1",
        false,
        stream_context_create($arrContextOptions)
    );

    $data1 = json_decode($json, true);

    if (!isset($data1["users"][0]["user_id"])) {
        echo "User not found";
        exit();
    }
    $user_id = $data1["users"][0]["user_id"];
} elseif (isset($_GET["id"])) {
    $user_id = $_GET["id"];
} else {
    echo "No user specified";
    exit();
}

($json = file_get_contents(
    "https://webapi.highrise.game/users/" . $user_id . "",
    false,
    stream_context_create($arrContextOptions)
)) or die("Error occured");

($data2 = json_decode($json, true)) or die("Invalid user id");

$userdata = $data2["user"];

if (isset($_GET["starts_after"])) {
    $starts_after = "&starts_after=" . $_GET["starts_after"];
} else {
    $starts_after = "";
}

if (isset($_GET["ends_before"])) {
    $ends_before = "&ends_before=" . $_GET["ends_before"];
} else {
    $ends_before = "";
}

$sort_order = "desc";

if (isset($_GET["sort_order"])) {
    $sort_order = $_GET["sort_order"];
}

$json = file_get_contents(
    "https://webapi.highrise.game/posts?limit=5" .
        $starts_after .
        $ends_before .
        "&sort_order=" .
        $sort_order .
        "&author_id=" .
        $user_id .
        "",
    false,
    stream_context_create($arrContextOptions)
);

$data3 = json_decode($json, true);

$totalposts = $data3["total"];

$posts = $data3["posts"];
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
			<h2 id="user-name"><?php echo $userdata["username"]; ?></h2>
			<?php if (isset($userdata["crew"]["name"])) { ?>
			<h6><?php echo $userdata["crew"]["name"]; ?></h6>
			<?php } ?>			
			<p id="user-bio" class="lead"><?php echo $userdata["bio"]; ?></p>
			<div class="container-fluid">
			<?php if (isset($userdata["discord_id"])) { ?>
			<h1><a href="https://discordapp.com/users/<?php echo $userdata[
       "discord_id"
   ]; ?>" class="purple"><i class="bi-discord"></i></a></h1>
			<?php } ?>
				<div class="row">
					<div class="col-md-3">
					<p><i class="bi-clock purple"></i> Joined on <strong><?php echo substr(
         $userdata["joined_at"],
         0,
         10
     ); ?></strong></p>
					</div>
					<div class="col-md-3">
					<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata[
         "num_following"
     ]; ?></strong> following</p>
					</div>
							<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata[
            "num_followers"
        ]; ?></strong> followers</p>

					</div>
					<div class="col-md-3">
								<p><i class="bi-person-fill purple"></i> <strong><?php echo $userdata[
            "num_friends"
        ]; ?></strong> friends</p>

					</div>
			
				</div>
			</div>	
		</section>
		
<div class="container-fluid pt-3 pb-2 text-left text-light">
		
<div class="row px-4">

<div class="col-9 py-3 mb-4 text-left text-light">
<ul class="nav nav-pills">
  <li class="nav-item">
    <a class="nav-link" href="profile.php?id=<?php echo $user_id; ?>">Posts</a>
  </li>
  <li class="nav-item">
    <a class="nav-link" href="profile.php?rooms&id=<?php echo $user_id; ?>">Rooms</a>
  </li>
  <li class="nav-item">
    <a class="nav-link active" href="stats.php?id=<?php echo $user_id; ?>">Stats</a>
  </li>
</ul>					
</div>
<div class="col-3 py-3 text-right">
</div>
		
</div>

<div class="container-fluid">

<div class="stats">

	<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Total posts</h4>
	<p><?php echo $totalposts; ?></p>
	</div>	
	</div>
		
	<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Most frequent words used</h4>
	<p class="text-muted">(In the most recent 75 posts, ignoring common words like 'and')</p>
	<a href="javascript:void(0);" role="button" class="btn btn-secondary mb-3" 
onclick="$( '#stats-1' ).html( 'Please wait..' );$( '#stats-1' ).load( 'statcalc.php?id=<?php echo $user_id; ?>&stat=1' );">Find out!</a>
	<div id="stats-1"></div>
	</div>	
</div>
		
		
	<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Post with the highest amount of <strong>likes</strong></h4>
	<p class="text-muted">(In the most recent 100 posts)</p>
<a href="javascript:void(0);" role="button" class="btn btn-secondary mb-3" 
onclick="$( '#stats-2' ).html( 'Please wait..' );$( '#stats-2' ).load( 'statcalc.php?id=<?php echo $user_id; ?>&stat=2' );">Find out!</a>
	<div id="stats-2"></div>
	</div>	
		</div>
		
	<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Post with the highest amount of <strong>comments</strong></h4>
	<p class="text-muted">(In the most recent 100 posts)</p>
<a href="javascript:void(0);" role="button" class="btn btn-secondary mb-3" 
onclick="$( '#stats-3' ).html( 'Please wait..' );$( '#stats-3' ).load( 'statcalc.php?id=<?php echo $user_id; ?>&stat=3' );">Find out!</a>
	<div id="stats-3"></div>
	</div>	
		</div>	
		
		<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Most used hashtags</h4>
	<p class="text-muted">(In the most recent 75 posts)</p>
<a href="javascript:void(0);" role="button" class="btn btn-secondary mb-3" 
onclick="$( '#stats-4' ).html( 'Please wait..' );$( '#stats-4' ).load( 'statcalc.php?id=<?php echo $user_id; ?>&stat=4' );">Find out!</a>
	<div id="stats-4"></div>
	</div>	
		</div>	
		
	<div class="box-shadow rounded-card mb-4">	
	<div class="card-body bg-dark text-light">
	<h4 class="purple">Most mentioned users</h4>
	<p class="text-muted">(The users that <?php echo $userdata[
     "username"
 ]; ?> have mentioned the most in the most recent 75 posts)</p>
<a href="javascript:void(0);" role="button" class="btn btn-secondary mb-3" 
onclick="$( '#stats-5' ).html( 'Please wait..' );$( '#stats-5' ).load( 'statcalc.php?id=<?php echo $user_id; ?>&stat=5' );">Find out!</a>
	<div id="stats-5"></div>
	</div>	
		</div>	
		
				
				
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
