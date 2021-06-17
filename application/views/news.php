<div class="smx-ui-title">
    <button type="button" class="hamburger animated fadeInLeft is-closed" data-toggle="offcanvas">
        <span class="hamb-top"></span>
        <span class="hamb-middle"></span>
        <span class="hamb-bottom"></span>
    </button>
    <h2 id="smx-title">NEWS</h2>
</div>


<div class="container">

    <?php foreach ($news as $article): ?>
        <div class="update-note">
            <p><?= $article['text'] ?></p>
        </div>
    <?php endforeach; ?>

</div>

