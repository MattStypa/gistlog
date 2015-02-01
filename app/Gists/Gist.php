<?php namespace Gistlog\Gists;

use Carbon\Carbon;
use Michelf\MarkdownExtra;

class Gist
{
    public $id;
    public $title;
    public $content;
    public $author;
    public $avatarUrl;
    public $link;
    private $public;

    /**
     * @var Carbon
     */
    public $createdAt;

    /**
     * @var Carbon
     */
    public $updatedAt;

    /**
     * @var Collection
     */
    public $comments;

    /**
     * @param array|ArrayAccess $githubGist
     * @param array|ArrayAccess $githubComments
     * @return Gist
     */
    public static function fromGitHub($githubGist, $githubComments = [])
    {
        $gist = new self;

        $gist->id = $githubGist['id'];
        $gist->title = $githubGist['description'];
        $gist->content = array_values($githubGist['files'])[0]['content'];
        $gist->author = $githubGist['owner']['login'];
        $gist->avatarUrl = $githubGist['owner']['avatar_url'];
        $gist->link = $githubGist['html_url'];
        $gist->public = $githubGist['public'];
        $gist->createdAt = Carbon::parse($githubGist['created_at']);
        $gist->updatedAt = Carbon::parse($githubGist['updated_at']);

        $gist->comments = collect($githubComments)->map(function ($comment) {
            return Comment::fromGitHub($comment);
        });

        return $gist;
    }

    /**
     * @return string
     */
    public function renderHtml()
    {
        return MarkdownExtra::defaultTransform($this->content);
    }

    /**
     * @return bool
     */
    public function hasComments()
    {
        return $this->comments->count() > 0;
    }

    /**
     * @return bool
     */
    public function isPublic()
    {
        return $this->public;
    }

    /**
     * @return bool
     */
    public function isSecret()
    {
        return ! $this->isPublic();
    }
}
