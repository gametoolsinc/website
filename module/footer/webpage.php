<footer>
    <div class="footer_wrapper">
        <div class="information">
            <h2>About us</h2>
            <?php
            $texts = [
                "Gamertools is a website full of tools for various games. These tools try to make the gaming experience better for you! We do this by calculating the most optimal way to do things or by explaining to you how something works."
            ];
            $text = $texts[array_rand($texts)];
            echo "<p>".$text."</p>";
            ?>
        </div>
        <!-- <div class="social">
            <h2>Social</h2>
            <div class="social_icons">
                <p>No social yet</p>

                HTML: <a><img src="{link}"></a>
            </div>
        </div> -->
        <div class="contact">
            <h2>Contact</h2>
            <div class="contact_links">
                <div class="contact_link">
                    <img loading="lazy" src="/module/footer/emailIcon.svg" alt="email icon">
                    <a href="mailto:gametoolsinc@gmail.com">gametoolsinc@gmail.com</a>
                </div>
            </div>
        </div>
        <div class="policies">
            <h2>Policies</h2>
            <?php
            $url = Webpage::upgradeUrl("/articles/1661622627");
            echo "<p><a href='$url'>Cookie policy</a></p>";
            $url = Webpage::upgradeUrl("/articles/1661622858");
            echo "<p><a href='$url'>Privacy policy</a></p>"; 
            ?>
            <p><a onclick="return klaro.show();" href="#">Change cookie preferences</a></p>
        </div>
    </div>
    <div class="copyright">
        <?php
            $year = date("Y");
            echo "<p>© GamerTools 2021-$year</p>";
        ?>
    </div>
</footer>