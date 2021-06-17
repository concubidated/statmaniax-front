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
                <button type="submit" name="search" class="btn btn-lg btn-success">Text Search</button>
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
    <p class="smx-font" style="color: white">Enter a search query to find players.</p>

</div>

