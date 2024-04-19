<?php

$arrContextOptions = [
    "ssl" => [
        "verify_peer" => false,
        "verify_peer_name" => false,
    ],
];

if (isset($_GET['stat']) && isset($_GET['id'])) {
    $stat = $_GET['stat'];
    $id = $_GET['id'];

    if ($stat == "1") {
        $json = file_get_contents('https://webapi.highrise.game/posts?limit=75&sort_order=desc&author_id=' . $id . '', false, stream_context_create($arrContextOptions));

        $data3 = json_decode($json, true);


        $posts = $data3["posts"];

        $commonWords = ['and', 'or', 'in', 'the', 'a', 'so', 'an', 'if', 'this', 'that', 'with', 'for', 'to', 'at', 'on', 'of', 'by', 'as', ',', ':', '&', '|', ' ', 'am', 'are', 'is', 'be', 'have', 'has']; // Common words to ignore
        $pronouns = ['i', 'you', 'he', 'she', 'it', 'we', 'they', 'my', 'your', 'their', 'our', 'me', 'him', 'her', 'us', 'them','i\'m','it\'s',"i’m","it’s"]; // Pronouns to ignore
        $wordCounts = [];

        $sentences = [];

        foreach ($posts as $post) {
            $sentence = "";
            if ($post['type'] == "photo" or $post['type'] == "video") {
                $sentence = $post['caption'];
            } elseif ($post['type'] == "text") {
                $sentence = $post['body']['text'];
            }

            $sentence = str_replace("\n", "", $sentence);
            $sentence = str_replace("\t", "", $sentence);
            $words = preg_split('/\s+/', $sentence); 

            foreach ($words as $word) {
                $word = strtolower($word); 

                // Ignore pronouns and common words
                if (strlen($word)>0 && !in_array($word, $pronouns) && !in_array($word, $commonWords)) {
                    if (isset($wordCounts[$word])) {
                        $wordCounts[$word]++;
                    } else {
                        $wordCounts[$word] = 1;
                    }
                }
            }
        }

        arsort($wordCounts);

        $amount = 0;
        foreach ($wordCounts as $word => $count) {
            echo "<h6>$word: $count occurances</h6>";
            $amount++;
            if ($amount == 10) {
                break;
            }
        }
    } elseif ($stat == "2") {
        $json = file_get_contents('https://webapi.highrise.game/posts?limit=100&sort_order=desc&author_id=' . $id . '', false, stream_context_create($arrContextOptions));

        $data3 = json_decode($json, true);

        $posts = $data3["posts"];

        $highest_likes = 0;
        $post_text = "";
        $post_id = "";
        foreach ($posts as $post) {
            if ($post['num_likes'] > $highest_likes) {
                $highest_likes = $post['num_likes'];
                if ($post['type'] == "photo" or $post['type'] == "video") {
                    $post_text = $post['caption'];
                } elseif ($post['type'] == "text") {
                    $post_text = $post['body']['text'];
                }
                $post_id = $post['post_id'];
            }
        }

        if ($post_id != "") {
            echo '<h5 class="lead">' . $post_text . '</h5>';
            echo '<h6 class="text-light">Likes: ' . $highest_likes . '</h6>';
            echo '<h4><a href="post.php?id=' . $post_id . '" class="purple">View post</a></h4>';
        } else {
            echo "<h5>Post not found</h5>";
        }
    } elseif ($stat == "3") {
        $json = file_get_contents('https://webapi.highrise.game/posts?limit=100&sort_order=desc&author_id=' . $id . '', false, stream_context_create($arrContextOptions));

        $data3 = json_decode($json, true);

        $posts = $data3["posts"];

        $highest_comments = 0;
        $post_text = "";
        $post_id = "";
        foreach ($posts as $post) {
            if ($post['num_comments'] > $highest_comments) {
                $highest_comments = $post['num_comments'];
                if ($post['type'] == "photo" or $post['type'] == "video") {
                    $post_text = $post['caption'];
                } elseif ($post['type'] == "text") {
                    $post_text = $post['body']['text'];
                }
                $post_id = $post['post_id'];
            }
        }

        if ($post_id != "") {
            echo '<h5 class="lead">' . $post_text . '</h5>';
            echo '<h6 class="text-light">Comments: ' . $highest_comments . '</h6>';
            echo '<h4><a href="post.php?id=' . $post_id . '" class="purple">View post</a></h4>';
        } else {
            echo "<h5>Post not found</h5>";
        }
    }
	elseif($stat==4) {

    $json = file_get_contents('https://webapi.highrise.game/posts?limit=75&sort_order=desc&author_id=' . $id . '', false, stream_context_create($arrContextOptions));

    $data3 = json_decode($json, true);

    $posts = $data3["posts"];

    $commonWords = ['and', 'or', 'in', 'the', 'a', 'so', 'an', 'if', 'this', 'that', 'with', 'for', 'to', 'at', 'on', 'of', 'by', 'as', ',', ':', '&', '|', ' ', 'am', 'are', 'is', 'be', 'have', 'has']; // Common words to ignore
    $pronouns = ['i', 'you', 'he', 'she', 'it', 'we', 'they', 'my', 'your', 'their', 'our', 'me', 'him', 'her', 'us', 'them','i\'m','it\'s',"i’m","it’s"]; // Pronouns to ignore
    $hashtagCounts = [];

    foreach ($posts as $post) {
        $sentence = "";
        if ($post['type'] == "photo" or $post['type'] == "video") {
            $sentence = $post['caption'];
        } elseif ($post['type'] == "text") {
            $sentence = $post['body']['text'];
        }

        $sentence = str_replace("\n", "", $sentence);
        $sentence = str_replace("\t", "", $sentence);
        preg_match_all('/#\w+/', $sentence, $hashtags); 

        foreach ($hashtags[0] as $hashtag) {
            $hashtag = strtolower($hashtag); 

            
            if (strlen($hashtag) > 0) {
                if (isset($hashtagCounts[$hashtag])) {
                    $hashtagCounts[$hashtag]++;
                } else {
                    $hashtagCounts[$hashtag] = 1;
                }
            }
        }
    }

    arsort($hashtagCounts);

    $amount = 0;
    foreach ($hashtagCounts as $hashtag => $count) {
        echo "<h6>$hashtag: $count uses</h6>";
        $amount++;
        if ($amount == 10) {
            break;
        }
    }
	if($amount==0)
	echo "<h6>No hashtags found</h6>";
}



	elseif($stat==5) {

    $json = file_get_contents('https://webapi.highrise.game/posts?limit=75&sort_order=desc&author_id=' . $id . '', false, stream_context_create($arrContextOptions));

    $data3 = json_decode($json, true);

    $posts = $data3["posts"];

    $mentionCounts = [];

    foreach ($posts as $post) {
        $sentence = "";
        if ($post['type'] == "photo" or $post['type'] == "video") {
            $sentence = $post['caption'];
        } elseif ($post['type'] == "text") {
            $sentence = $post['body']['text'];
        }

        $sentence = str_replace("\n", "", $sentence);
        $sentence = str_replace("\t", "", $sentence);
        preg_match_all('/\@\w+/', $sentence, $mentions);

        foreach ($mentions[0] as $mention) {
            $mention = strtolower($mention);

            
            if (strlen($mention) > 0) {
                if (isset($mentionCounts[$mention])) {
                    $mentionCounts[$mention]++;
                } else {
                    $mentionCounts[$mention] = 1;
                }
            }
        }
    }

    arsort($mentionCounts);

    $amount = 0;
    foreach ($mentionCounts as $mention => $count) {
        echo "<h6>$mention: $count mentions</h6>";
        $amount++;
        if ($amount == 10) {
            break;
        }
    }
	if($amount==0)
	echo "<h6>No mentions found</h6>";
}






}
?>
