<?php
// List of ~200 simple common words (neutral, non-branded)
function get_recovery_word_list(): array {
    return [
        'apple','arrow','baby','baker','balance','banana','beach','bean','bird','blanket','blue','board','boat','bottle','bread','bridge','bright','bubble','bucket','cable','cactus','candle','carpet','carrot','castle','cat','chair','chalk','cherry','chess','cloud','coffee','coin','cookie','copper','cotton','couch','cream','crystal','cup','curtain','daisy','dance','desert','diamond','dinner','dog','dolphin','door','dragon','drawer','dream','dress','drift','drop','eagle','earth','ember','engine','feather','field','figure','fire','fish','flame','flower','forest','fork','frozen','garden','gate','glass','globe','goat','gold','grape','grass','green','hammer','harbor','hat','honey','horse','house','ice','island','jelly','jewel','juice','kangaroo','kettle','key','kite','kitten','ladder','lake','lamp','lemon','letter','light','lion','maple','marble','meadow','mirror','mist','moon','mountain','mouse','music','needle','nest','night','north','oasis','ocean','olive','onion','orange','owl','panda','paper','peach','pearl','pencil','pepper','petal','piano','pillow','pine','planet','plate','plum','pond','pony','poppy','potato','pumpkin','puzzle','quartz','queen','quiet','rabbit','raven','river','robot','rock','roof','rose','saddle','safari','sand','seed','shadow','shell','ship','shoe','silver','sky','smile','smoke','snail','snow','soap','sock','spoon','spring','square','stack','star','steam','stone','straw','street','sugar','summer','sun','table','tiger','toast','tomato','tool','tower','train','tree','tulip','tunnel','valley','vanilla','vase','velvet','violet','violin','walnut','water','wave','wheel','whisper','white','willow','wind','window','winter','wolf','wood','yellow','yogurt','zebra','zephyr','amber','azure','brisk','calm','charm','dawn','dusk','ember','fable','gleam','harvest','harmony','ivory','jade','lilac','lumen','meadow','mellow','emberly','nectar','nova','opal','pebble','quill','ripple','serene','sprout','tidal','truffle','velour','verve','vista','whimsy','zenith'
    ];
}

function pick_random_words(int $n = 5): array {
    $src = get_recovery_word_list();
    $count = count($src);
    $n = max(1, min($n, $count));
    $used = [];
    $out = [];
    while (count($out) < $n) {
        try { $i = random_int(0, $count - 1); } catch (Exception $e) { $i = array_rand($src); }
        if (isset($used[$i])) continue;
        $used[$i] = true;
        $out[] = $src[$i];
    }
    return $out;
}
