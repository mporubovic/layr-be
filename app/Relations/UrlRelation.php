<?php


namespace App\Relations;

use App\Models\Card;
use App\Models\Content\Url;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class UrlRelation extends Relation
{

    
    // Adopted from https://stackoverflow.com/questions/59285324/laravel-one-to-one-relation-through-pivot-table-with-eager-load
    // and https://stitcher.io/blog/laravel-custom-relation-classes
    
    protected $query; 

    protected $card;

    public function __construct(Card $card)
    {
        parent::__construct(Url::query(), $card);
    }

    /**
     * @inheritDoc
     */
    public function addConstraints()
    {
        $this->query
            ->join(
                'card_content',
                'card_content.content_id',
                '=',
                'urls.id'
            )->where(
                'card_content.content_type',
                '=',
                'url'
            )->orderBy('card_content.content_position');
    }

    /**
     * @inheritDoc
     */
    public function addEagerConstraints(array $cards)
    {
        $this->query
            ->whereIn(
                'card_content.card_id',
                collect($cards)->pluck('id')
            );
    }

    /**
     * @inheritDoc
     */
    public function initRelation(array $cards, $relation)
    {
        foreach ($cards as $card) {
            $card->setRelation(
                $relation,
                null
            );
        }
        return $cards;
    }

    /**
     * @inheritDoc
     */
    public function match(array $cards, Collection $urls, $relation)
    {
        if ($urls->isEmpty()) {
            return $cards;
        }

        foreach ($cards as $card) {
            $card->setRelation(
                $relation,
                $urls->filter(function (Url $url) use ($card) {
                    return $url->card_id === $card->id;  // `card_id` came with the `join` on `card_content`
                })
            );
        }

        return $cards;
    }

    /**
     * @inheritDoc
     */
    public function getResults()
    {
        return $this->query->get();
    }
}