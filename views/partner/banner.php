<div id="mainContainer" class="clearfix" data-plugins="main">

<div role="content"><div role="main"><div class="pal mal">
    สร้างบล๊อคโฆษณา
    <hr/>
    เลือกขนาด

    <div>
        <ul>
            <li>120 x 400 <a class="ShowBanner" href="<?= IMAGES . 'banner/120x400.jpg' ?>" data-id='<?= $_COOKIE[COOKIE_KEY_PARTNER] ?>'>ตัวอย่าง</a></li>
            <li>300 x 300 <a class="ShowBanner" href="<?= IMAGES . 'banner/300x300.jpg' ?>" data-id='<?= $_COOKIE[COOKIE_KEY_PARTNER] ?>'>ตัวอย่าง</a></li>
            <li>600 x 120 <a class="ShowBanner" href="<?= IMAGES . 'banner/600x120.jpg' ?>" data-id='<?= $_COOKIE[COOKIE_KEY_PARTNER] ?>'>ตัวอย่าง</a></li>
            <li>728 x 90 <a class="ShowBanner" href="<?= IMAGES . 'banner/728x90.jpg' ?>" data-id='<?= $_COOKIE[COOKIE_KEY_PARTNER] ?>'>ตัวอย่าง</a></li> 
        </ul>
    </div>
    <div id="Boxx" style="background-color: #eee;border: 1px #cccccc solid;padding: 10px;margin-top: 30px;display: none;">
        <div id="showsample" style="padding: 20px;"> 


        </div>
        Code สำหรับติดหน้าเว็บ
        <textarea id="CodeSHow" style="padding: 20px;width: 100%;background-color: #fff;"></textarea>
        <button class="btn btn-blue" id="copycode">COPY CODE</button>
    </div>
    <script>
        $('.ShowBanner').click(function (e) {
            var link = $(this).attr('href');
            var agid = $(this).attr('data-id');
            $('#showsample').html("<img src='" + link + "'  style='border:1px solid #000'/>");
            $('#CodeSHow').html('<a href="<?= URL ?>partner/redirect/' + agid + '"><img src="' + link + '" /></a>');
            $('#Boxx').show();
            e.preventDefault();

        });

        var copyTextareaBtn = document.querySelector('#copycode');

        copyTextareaBtn.addEventListener('click', function (event) {
            var copyTextarea = document.querySelector('#CodeSHow');
            copyTextarea.select();

            try {
                var successful = document.execCommand('copy');
                var msg = successful ? 'successful' : 'unsuccessful';

            } catch (err) {

            }
        });
    </script>

</div></div></div>

</div>