<?php

namespace Dotdigitalgroup\B2b\Plugin;

use Dotdigitalgroup\B2b\Model\NegotiableQuoteRepository;
use Dotdigitalgroup\Email\Model\Resetter;

class ResetTypePlugin
{
    /**
     * @var NegotiableQuoteRepository
     */
    private $negotiableQuote;

    /**
     * ResetTypePlugin constructor.
     * @param NegotiableQuoteRepository $negotiableQuote
     */
    public function __construct(
        NegotiableQuoteRepository $negotiableQuote
    ) {
        $this->negotiableQuote = $negotiableQuote;
    }

    /**
     * @param Resetter $resetter
     * @param array $result
     * @return array[]
     */
    public function beforeSetResetModels(Resetter $resetter, array $result)
    {
        $result += get_object_vars($this);
        return [$result];
    }
}
