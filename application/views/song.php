<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title">SONG</h2>
</div>
 <div class="container-fluid">
    <div class="row">
        <div class="col-lg-3 col-md-5 smx-cover-pers">
            <img class="smx-cover" src="https://data.stepmaniax.com/<?= $song['cover_path'] ?>/cover.png" width="100%">
            <div style="padding-top: 25px"></div>
            <h1 class="smx-font"><?= $song['title'] ?></h1>
            <h4><?=$song['artist']?> // <?=$song['genre']?></h4>
             <table class="table">
                <thead>
                    <td><b>Beginner</b></td>
                    <td><b>Easy</b></td>
                    <td><b>Hard</b></td>
                    <td style="border-right: 1px white solid;"><b>Wild</b></td>
                    <td>Dual</td>
                    <td>Full</td>
                    <td>Team</td>
                </thead>
                <tbody>
                <tr>
                    <td><?=$song['basic']?></td>
                    <td><?=$song['easy']?></td>
                    <td><?=$song['hard']?></td>
                    <td style="border-right: 1px white solid;"><?= $song['wild'] ?></td>
                    <td><?=$song['dual']?></td>
                    <td><?=$song['full']?></td>
                    <td><?=$song['team']?></td>
                </tr>
                </tbody>
            </table>
         </div>
        <div class="col-lg-9 col-md-7">
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <strong>Tip:</strong> Hover over your score to view detailed judgement information.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <p class="smx-font" style="color: white">Currently displaying scores for
                <select class="form-control" onchange="setDifficulty();" id="difficulty">
                    <option <?php if ($diff == "basic"): ?> selected="selected" <?php endif; ?> value="basic">Beginner
                    </option>
                    <option <?php if ($diff == "easy"): ?> selected="selected" <?php endif; ?> value="easy">Easy
                    </option>
                    <option <?php if ($diff == "hard"): ?> selected="selected" <?php endif; ?> value="hard">Hard
                    </option>
                    <option <?php if ($diff == "wild"): ?> selected="selected" <?php endif; ?> value="wild">Wild
                    </option>
                    <option <?php if ($diff == "dual"): ?> selected="selected" <?php endif; ?> value="dual">Dual
                    </option>
                    <option <?php if ($diff == "full"): ?> selected="selected" <?php endif; ?> value="full">Full
                    </option>
                </select>
                mode. Select another difficulty to view scores for it.
            </p>

            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="hiscores-tab" data-toggle="tab" href="#hiscores" role="tab"
                       aria-controls="hiscores" aria-selected="true">Hi-Scores</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab"
                       aria-controls="history" aria-selected="false">History</a>
                </li>
            </ul>
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="hiscores" role="tabpanel" aria-labelledby="hiscores-tab">
                    <table class="table table-dark" data-sorting="true" data-paging="true">
                        <thead>
                        <th class="smx-font">Player</th>
                        <th class="smx-font" data-type="number">Score</th>
                        <th class="smx-font" data-breakpoints="xs sm">Grade</th>
                        <th class="smx-font" data-type="date" data-breakpoints="xs sm">Date</th>
                        </thead>
                        <tbody>
                        <?php foreach ($scores as $score) {
								if(!$score['picture_path'])
									$score['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";;
						?>

                            <tr>
                                <td data-sort-value="<?=$score['username'] ?>" class="smx-font">
                                    <img style="border-radius: 100px; margin-right: 20px;"
                                         src="https://data.stepmaniax.com/<?= $score['picture_path'] ?>"
                                         width="35px"> <a style="color: white; text-decoration: underline"
                                                          href="<?= base_url('player/' . $score['gamer_id']) ?>"> <?= $score['username'] ?></a>
                                </td>
                                <td class="smx-font" data-toggle="tooltip" data-placement="bottom" data-html="true"
                                    title="Grading:
Perfect!!: <?= $score['perfect1'] ?><br/>Perfect!: <?= $score['perfect2'] ?>
<br/>Early: <?= $score['early'] ?>
<br/>Late: <?= $score['late'] ?>
<br/>Miss: <?= $score['misses'] ?>"><?= $score['score'] ?></td>
                                <td class="smx-font"><img src="<?= $this->data->gradetostars($score['grade']) ?>"
                                                          width="35px"></td>
                                <td class="smx-font"><?= $score['created_at'] ?></td>
                            </tr>

                        <?php }


                        ?>

                        </tbody>
                    </table>
                </div>
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <table class="table table-dark" data-sorting="true" data-paging="true">
                        <thead>
                        <th class="smx-font">Player</th>
                        <th class="smx-font" data-type="number">Score</th>
                        <th class="smx-font" data-breakpoints="xs sm">Grade</th>
                        <th class="smx-font" data-type="date" data-breakpoints="xs sm">Date</th>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($score_history as $score) {
                                if(!$score['picture_path'])
                                    $score['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";;
                        ?>
                            <tr>
                                <td data-sort-value="<?=$score['username'] ?>" class="smx-font">
                                    <img style="border-radius: 100px; margin-right: 20px;"
                                         src="https://data.stepmaniax.com/<?= $score['picture_path'] ?>"
                                         width="35px"> <a style="color: white; text-decoration: underline"
                                                          href="<?= base_url('player/' . $score['gamer_id']) ?>"><?= $score['username'] ?></a>
                                </td>
                                <td class="smx-font" data-toggle="tooltip" data-placement="bottom" data-html="true"
                                    title="Grading:
Perfect!!: <?= $score['perfect1'] ?><br/>Perfect!: <?= $score['perfect2'] ?>
<br/>Early: <?= $score['early'] ?>
<br/>Late: <?= $score['late'] ?>
<br/>Miss: <?= $score['misses'] ?>"><?= $score['score'] ?></td>
                                <td><img src="<?= $this->data->gradetostars($score['grade']) ?>" width="35px"></td>
                                <td class="smx-font"><?= $score['created_at'] ?></td>
                            </tr>

                        <?php }


                        ?>

                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
 </div>

<script>
    jQuery(function ($) {
        $('.table').footable({
            "expandFirst": false,
            "showToggle": false
        });
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    function setDifficulty() {
        var diff = document.getElementById("difficulty").value;
        window.location = "<?=base_url('song/' . $songid)?>/" + diff;
    }
</script>
