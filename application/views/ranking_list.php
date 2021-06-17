<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title">RANKING LIST</h2>
</div>


<div class="container">
    <p class="smx-font" style="color: white">Currently displaying ranking information for
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
            <option <?php if ($diff == "wildfull"): ?> selected="selected" <?php endif; ?> value="wildfull">Wild + Full
            </option>
            <option <?php if ($diff == "total"): ?> selected="selected" <?php endif; ?> value="total">Total
            </option>
        </select>
        mode.
    </p>
    <div class="userlist-ui">

        <?php
        $rank = 1;
        foreach ($rankings as $user):
		if (!$user['picture_path'])
			$user['picture_path'] = "uploads/b72a65b1910f794996364e8fdd25216bf84e2bb7.jpg";
		?>

                <div class="userlist-user">
                    <div class="row">
                        <div class="col-2">
                            <img class="hide-on-small" src="https://data.stepmaniax.com/<?= $user['picture_path'] ?>"
                                 width="100">
                        </div>
                        <div class="col-10">
                            <div class="smx-userlist-container">
                                <div class="center smx-font">
                                    <a href="<?= base_url('player/' . $user['id']) ?>"><h2>#<?= $rank ?>
                                            : <?= $user['username'] ?></h2></a>
                                    <p>Rank Points: <?= number_format($user['rank']) ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <?php $rank++;  endforeach; ?>

        <div>
        </div>
    </div>
<script>
    function setDifficulty() {
        var diff = document.getElementById("difficulty").value;
        window.location = "<?=base_url('ranking')?>/" + diff;
    }
</script>
