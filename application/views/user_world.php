<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title"><?= $user_info['username'] ?></h2>
</div>

<a href="<?= base_url('player/' . $userid . '/' . $diff) ?>" class="back-button"><img
            src="<?= base_url('assets/img/back.png') ?>" width="158px"></a>

<div class="container-fluid row">
    <div class="col-md-2">
        <div class="player-info">
            <img src="https://data.stepmaniax.com/<?= $user_info['picture_path'] ?>" width="100%">
            <hr>
            <h6 class="smx-font">Total World Records: <span id="worldrecord-count">??</span></h6>
            <h6 class="smx-font">Country: <?= $user_info['country'] ?></h6>
            <h6 class="smx-font">Total: <?= number_format($user_info['total_score']) ?></h6>
            <hr>
            <h6 class="smx-font">Stars Earned (All Time)</h6>
            <table class="table">
                <?php foreach ($user_stats as $stat): ?>

                    <tr>
                        <td><img src="<?= $this->data->gradetostars($stat['grade']); ?> " width="50px"></td>
                        <td class="smx-font"
                            style="vertical-align: middle; text-align: center"><?= $stat['count'] ?></td>
                    </tr>


                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <div class="col-md-10">


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
        <p class="smx-font" style="color: white">World mode enabled - Comparing scores to current world records.</p>

        <table class="table table-dark" data-sorting="true" data-paging="false" data-paging-size="25">
            <thead class="smx-font">
            <tr>
                <th class="truncate">Song Title</th>
                <th data-breakpoints="xs sm">Artist</th>
                <th data-type="number">Level</th>
                <th data-type="number">Score</th>
                <th data-type="number">Delta</th>
		<th data-type="number">Ranking Gap</th>
                <th data-breakpoints="xs sm">Grade</th>
                <th data-type="number" data-breakpoints="xs sm">WR</th>
                <th data-type="date" data-breakpoints="xs sm">Date</th>
            </thead>
            </tr>
            <tbody>
            <?php

            $wr = 0;
            foreach ($user_scores as $key => $score) {


                #print_r($score);

                $world = $world_scores[$key]['score'];
                $score_points = $score['score'];
                $delta = $world - $score_points;

                $deltadisplay = "<span class='delta-nowr'>-" . $delta . "</span>";


		$rankdiff = round(pow($score[$score['name']],2)*(-1*$delta)/1000);

                if ($delta == 0) {
                    $wr += 1;
                    $deltadisplay = "<span class='delta-wr'>0</span>";
                    echo "<tr class='wr smx-font'>";
                } else {
                    echo "<tr class='smx-font'>";
                }
                /*
                echo "<tr>";
                echo "<td>".$score['title']."</td>";
                echo "<td>".$score['artist']."</td>";
                echo "<td>".$score['score']."</td>";
                echo "<td>".$world."</td>";
                echo "<td>".$score['date']."</td>";
                echo "</tr>";

                <?=base_url('song/' . $this->data->get_song_id_by_title($score['title']))?>
                ^ don't use, slow. needs optimisation
                */

                ?>

                <td data-sort-value="<?=$score['title'] ?>" class="truncate-playerui"><a style="color: white; text-decoration: underline"
                                                 href="<?= base_url('song/' . $score['game_song_id'] . "/" . $score['name']) ?>"><?= $score['title'] ?></a>
                </td>
                <td class="truncate-playerui"><?= $score['artist'] ?></td>
                <td><?= $score[$score['name']] ?></td>
                <td data-toggle="tooltip" data-placement="bottom" data-html="true" title="Grading:
Perfect!!: <?= $score['perfect1'] ?><br/>Perfect!: <?= $score['perfect2'] ?>
<br/>Early: <?= $score['early'] ?>
<br/>Late: <?= $score['late'] ?>
<br/>Miss: <?= $score['misses'] ?>"><?= $score['score'] ?></td>
                <td><?= $deltadisplay ?></td>
		<td><?= $rankdiff ?></td>
                <td><img src="<?= $this->data->gradetostars($score['grade']) ?>" width="35px"></td>
                <td><?= $world ?></td>
                <td><?= $score['date'] ?></td>
                </tr>
                <?php

            }


            ?>

            </tbody>
        </table>

    </div>
</div>




<script>

    var wr = <?=$wr?>;

    jQuery(function ($) {
        $('.table').footable();
    });


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    })

    document.getElementById("worldrecord-count").innerHTML = wr;

    function setDifficulty() {
        var diff = document.getElementById("difficulty").value;
        window.location = "<?=base_url('player/' . $userid . '/compare/' . $rivalid)?>/" + diff;
    }
</script>
