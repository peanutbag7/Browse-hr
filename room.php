
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
    $room_id = $_GET['id'];
} else {
    echo "No room id was specified";
    exit();
}

($json = file_get_contents('https://webapi.highrise.game/rooms/' . $room_id . '', false, stream_context_create($arrContextOptions))) or die("Error occured");

($data = json_decode($json, true)) or die("Room not found");

$room = $data["room"];

($json = file_get_contents('https://webapi.highrise.game/users/' . $room['owner_id'] . '', false, stream_context_create($arrContextOptions))) or die('Error occured');

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

			
	





				<div class="rooms">
<?php



		$room_id = $room['room_id'];
        ?>
	<div class="box-shadow rounded-card mb-4">
	
	<div class="card-body bg-dark text-light">
	
<div class="row">
<div class="col-3">
<?php
// https://d4v5j9dz6t9fz.cloudfront.net/default_room_images/
if(isset($room['thumbnail_url'])) { ?>
<img alt="room-thumbnail" src="<?php echo $room['thumbnail_url']; ?>" style="width:100%;" class="rounded-corners">
<?php ;} elseif (isset($room['banner_url'])) { ?>
<img alt="room-banner" src="<?php echo $room['banner_url']; ?>" style="width:100%;" class="rounded-corners">
<?php ;} ?>
</div>
	
	<div class="col-9">	
	<h2 class="room-name"><?php echo $room["disp_name"]; ?></h2>
	<h5 class="room-owner">By <a href="profile.php?id=<?php echo $room['owner_id']; ?>" class="purple"><?php echo $userdata["username"]; ?></a></h5>

	<?php if(isset($room["description"])){ ?><p class="text-muted"><?php echo $room["description"]; ?></p><?php ;} ?>
			
	</div>
	


</div>	

<br>	

	<h4 class="room-info"><i class="bi-person-fill purple"></i> <?php echo $room['num_connected']; ?> user in room</h4>
	


	</div>
	</div>
<?php
    
?>
						
					
					
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
