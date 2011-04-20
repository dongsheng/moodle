<?php

/**
 * Validate comment data before adding to database
 *
 * @param stdClass $comment
 * @param stdClass $args
 * @return boolean
 */
function comments_comment_add($comment, $args) {
    if ($comment->commentarea != 'page_comments') {
        throw new comment_exception('invalidcommentarea');
    }
    if ($comment->itemid != 0) {
        throw new comment_exception('invalidcommentitemid');
    }
    return true;
}
