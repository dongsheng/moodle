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

/**
 * Running addtional permission check on plugins
 *
 * @param stdClass $args
 * @return array
 */
function comments_comment_permissions($args) {
    return array('post'=>true, 'view'=>true);
}

/**
 * Validate comment data before displaying comments
 *
 * @param stdClass $comment
 * @param stdClass $args
 * @return boolean
 */
function comments_comment_display($comments, $args) {
    if ($args->commentarea != 'page_comments') {
        throw new comment_exception('invalidcommentarea');
    }
    if ($args->itemid != 0) {
        throw new comment_exception('invalidcommentitemid');
    }
    return $comments;
}
