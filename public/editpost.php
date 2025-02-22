<?php

use RA\Permissions;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../lib/bootstrap.php';

if (!authenticateFromCookie($user, $permissions, $userDetails)) {
    // Immediate redirect if we cannot validate cookie!	//TBD: pass args?
    header("Location: " . getenv('APP_URL') . "?e=notloggedin");
    exit;
}

$requestedComment = requestInputQuery('c', 0, 'integer');

if (getSingleTopicComment($requestedComment, $commentData) == false) {
    header("location: " . getenv('APP_URL') . "/forum.php?e=unknowncomment");
    exit;
}

if ($user != $commentData['Author'] && $permissions < Permissions::Admin) {
    header("Location: " . getenv('APP_URL') . "?e=nopermission");
    exit;
}

if (getTopicDetails($commentData['ForumTopicID'], $topicData) == false) {
    header("location: " . getenv('APP_URL') . "/forum.php?e=unknownforum2");
    exit;
}
$existingComment = $commentData['Payload'];
$thisForumTitle = $topicData['Forum'];
$thisTopicTitle = $topicData['TopicTitle'];
$thisTopicID = $commentData['ForumTopicID'];
$thisTopicAuthor = $topicData['Author'];
$thisAuthor = $commentData['Author'];
// $thisForumDescription = $topicData['ForumDescription'];
// $thisCategoryID = $topicData['CategoryID'];
// $thisCategoryName = $topicData['CategoryName'];

$errorCode = requestInputSanitized('e');

RenderHtmlStart();
RenderHtmlHead("Edit post");
?>
<body>
<?php RenderHeader($userDetails); ?>
<div id="mainpage">
    <div id="fullcontainer">
        <?php
        echo "<div class='navpath'>";
        echo "<b>Edit Post</b>";
        echo "</div>";

        echo "<h2 class='longheader'>Edit post</h2>";

        echo "<table>";
        echo "<tbody>";

        echo "<form action='/request/forum-topic/update.php' method='post'>";
        echo "<input type='hidden' value='$requestedComment' name='i'>";
        echo "<input type='hidden' value='$thisTopicID' name='t'>";
        // echo "<input type='hidden' value='$requestedForumID' name='f'>";
        echo "<tr>" . "<td>Forum:</td><td><input type='text' readonly value='$thisForumTitle'></td></tr>";
        echo "<tr>" . "<td>Topic:</td><td><input type='text' readonly class='fullwidth' value='$thisTopicTitle'></td></tr>";
        echo "<tr>" . "<td>Author:</td><td><input type='text' readonly value='$thisAuthor'></td></tr>";
        echo "<tr>" . "<td>Message:</td><td>";

        RenderShortcodeButtons();

        echo <<<EOF
            <textarea 
                id="commentTextarea" 
                class="fullwidth" 
                style="height:300px" 
                rows="32" cols="32" 
                name="p" 
                placeholder="Don't share links to copyrighted ROMs."
            >$existingComment</textarea></td></tr>
        EOF;
        echo "<tr>" . "<td></td><td class='fullwidth'><input type='submit' value='Submit post' SIZE='37'/>&nbsp;<a href='/viewtopic.php?t=$thisTopicID&c=$requestedComment'>Cancel</a></td></tr>";
        echo "</form>";

        echo "</tbody>";
        echo "</table>";
        ?>
        <br>
    </div>
</div>
<?php RenderFooter(); ?>
</body>
<?php RenderHtmlEnd(); ?>
