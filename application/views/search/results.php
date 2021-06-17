<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title">SEARCH</h2>
</div>


<div class="container">

    <form method="post">

        <div class="row">
            <div class="col-10">
                <input class="form-control form-control-lg" name="query" placeholder="Search for a player...">
            </div>
            <div class="col-2">
                <button type="submit" name="search" class="btn btn-lg btn-success">Search!</button>
            </div>
        </div>

    </form>
    <hr>
    <form method="post">

        <div class="row">
            <div class="col-10">
                <p class="smx-font" style="color: white">Search by country: <select name="country">
                        <?php foreach ($countries as $country):
                            if ($country['country'] == null) continue;
                            ?>
                            <option value="<?= $country['country'] ?>"><?= $country['country'] ?></option>
                        <?php endforeach; ?>
                    </select></p>
            </div>
            <div class="col-2">
                <button type="submit" name="search" class="btn btn-lg btn-success">Country Search</button>
            </div>
        </div>

    </form>
    <hr>
    <div class="userlist-ui" style="margin-top: 0">

        <?php foreach ($results as $user):
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
                                    <a href="player/<?= $user['id'] ?>"><h2><?= $user['username'] ?></h2></a>
                                    <p><?= number_format($user['total_score']) ?> Acc. Points</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            <?php  endforeach; ?>

        <div>

        </div>
    </div>

