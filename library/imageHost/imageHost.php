<?php

class ImageHost
{
    public static function getBetterUrl(string $url)
    {
        $urls = array(
            "/resources/articles/article.png" => "https://i.ibb.co/pL8KxyR/article.png",
            "/resources/games/astroneer/background/Astroneer_Image1.png" => "https://i.ibb.co/b7PHnxd/Astroneer-Image1.png",
            "/resources/games/codcoldwar/background/cold_war_1.jpg" => "https://i.ibb.co/FzKTSSZ/cold-war-1.jpg",
            "/resources/games/general/background/tree.jpg" => "https://i.ibb.co/d6PhkKf/tree.jpg",
            "/resources/games/gtaV/background/luxury-air-travel (1).jpg" => "https://i.ibb.co/cCt4dwh/luxury-air-travel-1.jpg",
            "/resources/games/mindustry/background/Background.jpg" => "https://i.ibb.co/CHcQ315/Background.jpg",
            "/resources/games/minecraft/background/2021-11-10_16.30.47.jpg" => "https://i.ibb.co/LJ0pYQN/2021-11-10-16-30-47.jpg",
            "/resources/games/minecraft/background/2021-11-10_16.31.51.jpg" => "https://i.ibb.co/VWyvNwK/2021-11-10-16-31-51.jpg",
            "/resources/games/minecraft/background/2021-11-10_16.24.52.jpg" => "https://i.ibb.co/TRQLnbG/2021-11-10-16-24-52.jpg",
            "/resources/games/minecraft/background/image-0.jpg" => "https://i.ibb.co/mB9srmG/image-0.jpg",
            "/resources/games/minecraft/background/image-3.jpg" => "https://i.ibb.co/Yygr84T/image-3.jpg",
            "/resources/games/yugioh/background/Some_Yu-Gi-Oh!_cards.jpg" => "https://i.ibb.co/R40kQHS/Some-Yu-Gi-Oh-cards.jpg",
            "/resources/applications/default/calculator_icon.png" => "https://i.ibb.co/1r9xWwc/calculator-icon.png",
            "/public/applications/direction/icon.png" => "https://i.ibb.co/jvksMT8/icon.png",
            "/module/footer/emailIcon.svg" => "https://i.ibb.co/D79Nt40/email-Icon.png",
            "/public/applications/scoreboard/icon.svg" => "https://i.ibb.co/V3QmC2S/icon.png",
            "/resources/main/logo.png" => "https://i.ibb.co/DtjwY68/logo.png",

            "/module/information/icons/bug.svg" => "https://i.ibb.co/vDvzXYL/bug.png",
            "/module/information/icons/star.svg" => "https://i.ibb.co/r2hjgbR/star.png",
            "/module/information/icons/info.svg" => "https://i.ibb.co/LJM8gNb/info.png",
            "/module/information/icons/why.png" => "https://i.ibb.co/1KYhbqJ/why.png",
            "/module/information/icons/what.png" => "https://i.ibb.co/5n9CFxH/what.png",
            "/module/information/icons/other.png" => "https://i.ibb.co/myNCsQ8/other.png",
            "/module/information/icons/how.png" => "https://i.ibb.co/9c6KKQB/how.png",
            "/module/information/icons/did.png" => "https://i.ibb.co/StP18Cz/did.png",
            "/module/information/icons/do.png" => "https://i.ibb.co/hfq1VYJ/do.png",

            "/resources/games/minecraft/user_interface/nether.jpg" => "https://i.ibb.co/HgCLHXk/nether.jpg",
            "/resources/games/minecraft/user_interface/overworld.jpg" => "https://i.ibb.co/BfPQq6X/overworld.jpg",
            "/resources/games/minecraft//icons.png" => "https://i.ibb.co/vj6pbw0/icons.png",
        );

        if (array_key_exists($url, $urls)) {
            return $urls[$url];
        } else {
            return $url;
        }
    }
}
