<html>
<head>
    <title>StatManiaX Embed</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">

    <link rel="icon" type="image/png" href="<?php echo base_url('assets/img/favicon.png') ?>"/>

    <link rel="stylesheet" href="/assets/css/footable.bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/custom.css">
    <link rel="stylesheet" href="/assets/css/all.css">
</head>
<body style="background: green">
<?php

$wrs = 0;

foreach ($user_stats as $stat):

    $stars[$stat['grade']] = $stat['count'];

endforeach;


if(!isset($total_records)){
	foreach ($user_scores as $key => $score) {
		$world = $world_scores[$key]['score'];
		$score_points = $score['score'];
		$delta = $world - $score_points;
		if ($delta == 0)
			$wrs += 1;
	}
} else {
	$wrs = $total_records;
}
?>

<div class="embed-container">
    <div style="background: white; color: black;">
        <div class="row">
            <div class="col-3">
                <img src="https://data.stepmaniax.com/<?php echo $user_info['picture_path'] ?>" width="100%">
            </div>
            <div class="col-9">
                <h2 class="smx-font" style="padding-top: 5%"><?php echo $user_info['username'] ?></h2>
                <h6 class="smx-font">Data provided by statmaniax.com</h6>

            </div>
        </div>
    </div>

    <div class="row" style="padding: 1.5%">
        <div class="col-6">
            <div class="row">
                <div class="col-6 smx-font">
                    <p><img src="<?php echo $this->data->gradetostars(0); ?> " width="35px"> <?php if($stars[0]) echo $stars[0]; else echo 0  ?></p>
                    <p><img src="<?php echo $this->data->gradetostars(1); ?> " width="35px"> <?php if($stars[1]) echo $stars[1]; else echo 0 ?></p>
                    <p><img src="<?php echo $this->data->gradetostars(2); ?> " width="35px"> <?php if($stars[2]) echo $stars[2]; else echo 0 ?></p>
                </div>
                <div class="col-6 smx-font">
                    <p><img src="<?php echo $this->data->gradetostars(3); ?> " width="35px"> <?php if($stars[3]) echo $stars[3]; else echo 0 ?></p>
                    <p><img src="<?php echo $this->data->gradetostars(4); ?> " width="35px"> <?php if($stars[4]) echo $stars[4]; else echo 0 ?></p>
                    <p><img src="<?php echo $this->data->gradetostars(5); ?> " width="35px"> <?php if($stars[5]) echo $stars[5]; else echo 0 ?></p>
                </div>
            </div>
        </div>
        <div class="col-6 smx-font">
            <?php if(isset($diff)): ?><p>Stats for <?php echo $diff ?></p> <?php endif; ?>
            <h6 class="smx-font">Country: <?php echo $user_info['country'] ?></h6>
            <h6 class="smx-font">Total Score: <?php echo number_format($user_info['total_score']) ?></h6>
            <h6 class="smx-font">World Records: <?php echo $wrs ?></h6>
        </div>
    </div>
</div>

</body>
</html>
