<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title"><?=$user_info['username']?></h2>
</div>
<div class="container-fluid row">
    <div class="col-md-2">
        <div class="player-info">
            <img src="https://data.stepmaniax.com/<?= $user_info['picture_path'] ?>" width="100%">
            <hr>
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
            <hr>
            <a class="btn btn-primary" href="<?= base_url('embed/' . $userid . '/' . $diff) ?>"><?= ucfirst($diff) ?>
                Mode Embed</a>
            <a class="btn btn-primary" href="<?= base_url('embed/' . $userid) ?>">Generic Embed</a>
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

        <div class="bg-dark" style="width: 100%; padding: 1%">

            <div class="row">
                <div class="col-md-3">
                    <a href="<?= base_url('player/' . $userid . '/compare/world/' . $diff) ?>" class="btn btn-primary ">
                        <i class="fas fa-globe-americas"></i> Compare to World Records
                    </a>
                </div>
                <div class="col-md-9">
                    <input class="form-control" id="rivalselect" type="text" name="rivalselect"
                           placeholder="Type to find rival.." autocomplete="off">
                </div>
            </div>


            <table class="table table-dark" data-sorting="true" data-paging="false" data-paging-size="25">
                <thead class="smx-font">
                <tr>
                    <th class="truncate">Song Title</th>
                    <th data-breakpoints="xs sm">Artist</th>
                    <th data-type="number">Level</th>
                    <th data-type="number">Score</th>
                    <th data-breakpoints="xs sm">Grade</th>

                    <th data-breakpoints="xs sm" data-type="date">Date</th>
                </thead>
                </tr>
                <tbody>
                <?php

                $wr = 0;
                foreach ($user_scores as $key => $score) {


                    #print_r($score);

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
                    <tr class='smx-font'>
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
                        <td><img src="<?= $this->data->gradetostars($score['grade']) ?>" width="35px"></td>
                        <td><?= $score['date'] ?></td>
                    </tr>
                    <?php

                }


                ?>

                </tbody>
            </table>

        </div>
    </div>
</div>




<script>

    var wr = <?=$wr?>;

jQuery(function($){
    $('.table').footable();
});


    $(function () {
        $('[data-toggle="tooltip"]').tooltip()
    });


    function setDifficulty() {
        var diff = document.getElementById("difficulty").value;
        window.location = "<?=base_url('player/' . $userid)?>/" + diff;
    }

    $('#rivalselect').typeahead({
        onSelect: function (item) {
            console.log(item);
            window.location = "<?=base_url('player/' . $userid . '/compare')?>" + "/" + item.value + "/<?=$diff?>";
        },
        ajax: {
            url: "<?=base_url('main/userlist')?>",
            timeout: 500,
            displayField: "username",
            triggerLength: 1,
            method: "post",
            loadingClass: "loading-circle",
            preDispatch: function (query) {
                return {
                    search: query
                }
            },
            preProcess: function (data) {
                console.log(data);
                if (data.success === false) {
                    // Hide the list, there was some error
                    return false;
                }
                // We good!
                return data;
            }
        }
    });
</script>
