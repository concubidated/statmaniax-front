<script>
    var trigger = $('.hamburger'),
        overlay = $('.overlay'),
        isClosed = false;

    trigger.click(function () {
        hamburger_cross();
    });

    function hamburger_cross() {

        if (isClosed == true) {
            overlay.hide();
            trigger.removeClass('is-open');
            trigger.addClass('is-closed');
            isClosed = false;
            $('#wrapper').toggleClass('toggled');
        } else {
            overlay.show();
            trigger.removeClass('is-closed');
            trigger.addClass('is-open');
            isClosed = true;
        }

    }

    $('[data-toggle="offcanvas"]').click(function () {
        $('#wrapper').toggleClass('toggled');
    });
</script>
</div>
<div class=footer><p class="copyright">StepmaniaX is &copy;2021 Step Revolution LLC. StatManiaX is developed by <a
            href="mailto:liam@namwen.me">Namwen</a> and <a href="http://concubidated.com">Concubidated</a> .</p>
</div>
</div>

</body>
</html> 
