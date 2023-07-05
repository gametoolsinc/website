<?php
include_once($_SERVER['DOCUMENT_ROOT'] . "/library/imageHost/imageHost.php");

?>

<footer>
    <div class="footer_wrapper">
        <div class="information">
            <h2>About us</h2>
            <?php
            $texts = [
                "Welcome to Gamertools, your one-stop website for enhancing your gaming experience! Our goal is to make your gaming experience the best it can be. Whether you need help with calculating something or want to understand the optimal strategy, our tools have got you covered.",
                "Gamertools is a website dedicated to elevating your gaming experience! Our objective is to provide you with the best gaming experience. Our tools cover a lot of different games. Let our tools help you in your gaming journey!",
                "Gamertools provides an array of tools for various games. Our tools are designed to enhance your gaming experience by enabling you to optimize your gaming strategy or improve your understanding of the game better.",
            ];
            $text = $texts[array_rand($texts)];
            echo "<p>" . $text . "</p>";
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
                    <img loading="lazy" src=<?= ImageHost::getBetterUrl("/module/footer/emailIcon.svg"); ?> alt="email icon">
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