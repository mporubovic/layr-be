<?php


namespace App\Relations;

use App\Models\Card;
use App\Models\Content\Embed;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class EmbedRelation extends Relation
{

    
    // Adopted from https://stackoverflow.com/questions/59285324/laravel-one-to-one-relation-through-pivot-table-with-eager-load
    // and https://stitcher.io/blog/laravel-custom-relation-classes
    
    protected $query; 

    protected $card;

    public function __construct(Card $card)
    {
        parent::__construct(Embed::query(), $card);
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
                'embeds.id'
            )->where(
                'card_content.content_type',
                '=',
                'embed'
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
    public function match(array $cards, Collection $embeds, $relation)
    {
        if ($embeds->isEmpty()) {
            return $cards;
        }

        foreach ($cards as $card) {
            $card->setRelation(
                $relation,
                $embeds->filter(function (Embed $embed) use ($card) {
                    return $embed->card_id === $card->id;  // `card_id` came with the `join` on `card_content`
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