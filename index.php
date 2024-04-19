<?php

$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

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


if(isset($_GET['rooms']))
{
$json = file_get_contents('https://webapi.highrise.game/rooms?limit=10' . $starts_after . $ends_before . '&sort_order=' . $sort_order . '', false, stream_context_create($arrContextOptions));

$data3 = json_decode($json, true);

$rooms = $data3["rooms"];
}

else
{
$json = file_get_contents('https://webapi.highrise.game/users?limit=10' . $starts_after . $ends_before . '&sort_order=' . $sort_order . '', false, stream_context_create($arrContextOptions));

$data3 = json_decode($json, true);

$users = $data3["users"];
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
		<section class="jumbotron mb-0 text-light text-center" style="padding-top:96px;position:relative;margin-top:0px;">
			<h2 id="site-heading">Browse Highrise project</h2>	
			<p id="site-desc" class="lead">An unofficial highrise data display using highrise webapi, coded by <a href="profile.php?username=peanutbag" class="purple">PeanutBag</a><br>This website is independent and not associated with Highrise. <a href="https://highrise.game" class="purple">Visit Highrise's official website</a></p>
			
			<form method="get" action="profile.php">
				<input type="text" name="username" placeholder="Enter username" class="bg-dark text-light">
				<input type="submit" class="btn btn-secondary" value="View" >
			</form>
		</section>
		
<div class="container-fluid pt-3 pb-2 text-left text-light">
		
<div class="row px-4">

<div class="col-9 py-3 mb-4 text-left text-light">
<ul class="nav nav-pills">
  <li class="nav-item">
    <a class="nav-link<?php if(!isset($_GET['rooms'])) echo ' active'; ?>" href="index.php?sort_order=<?php echo $sort_order ; ?>">Users</a>
  </li>
  <li class="nav-item">
    <a class="nav-link<?php if(isset($_GET['rooms'])) echo ' active'; ?>" href="index.php?rooms&sort_order=<?php echo $sort_order ; ?>">Rooms</a>
  </li>
</ul>					
</div>
		<div class="col-3 py-3 text-right">
		<?php if ($sort_order == "asc") { ?>
			<h4><a href="index.php?sort_order=desc" class="text-muted"><i class="bi-sort-down"></i></a></h4>
			<?php } else { ?>
				<h4><a href="index.php?sort_order=asc" class="text-muted"><i class="bi-sort-up"></i></a></h4>
		<?php } ?>
		</div>
		
</div>

<div class="container-fluid">
<?php
if(isset($_GET['rooms']))
{ ?>






				<div class="rooms">
<?php
$s_a = "";
$e_b = "";
for ($i = 0; $i < 10; $i++) {
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
	<?php if(isset($rooms[$i]["description"])){ ?><p class="text-muted"><?php echo $rooms[$i]["description"]; ?></p><?php ;} ?>
	
	

<br>	




	</div>
	</div>
<?php
    ;}
}
?>
						
					
					
<div class="text-center">
<h3><?php if (isset($_GET['starts_after'])) { ?><a class="text-muted mr-4" href="indez.php?rooms&sort_order=<?php echo $sort_order; ?>&ends_before=<?php echo $e_b; ?>">&laquo; Previous</a><?php ;} ?> 
<a class="text-muted" href="index.php?rooms&sort_order=<?php echo $sort_order; ?>&starts_after=<?php echo $s_a; ?>">Next &raquo;</a> </h3>
</div>
					
				</div>
	






	
<?php ;}else{ ?>
				<div class="users">
<?php
$s_a = "";
$e_b = "";
for ($i = 0; $i < 10; $i++) {
    $post_content = "";
    if (isset($users[$i])) {

        $s_a = $users[$i]['user_id'];
        if ($i == 0) {
            $e_b = $users[$i]['user_id'];
        }

		$user_id = $users[$i]['user_id'];
        ?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	
	
	<h4 class="user-name">
	<a href="profile.php?id=<?php echo $users[$i]['user_id'] ; ?>" class="purple"><?php echo $users[$i]['username']; ?></a>
	</h4>
	<p class="text-muted"><?php echo onlineTimeAgo($users[$i]['last_connect_at']); ?></p>

	




	</div>
	</div>
<?php
    }
}
?>
						
					
					
<div class="text-center">
<h3><?php if (isset($_GET['starts_after'])) { ?><a class="text-muted mr-4" href="index.php?sort_order=<?php echo $sort_order; ?>&ends_before=<?php echo $e_b; ?>">&laquo; Previous</a><?php } ?> 
<a class="text-muted" href="index.php?sort_order=<?php echo $sort_order; ?>&starts_after=<?php echo $s_a; ?>">Next &raquo;</a> </h3>
</div>
					
				</div>
	<?php ;} ?>			
				
				
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
