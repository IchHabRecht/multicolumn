<?php


namespace IchHabRecht\Multicolumn\Hooks;


class ContentUsedDecision
{

    public function isContentElementUsed(array $parameters): bool
    {
        return $parameters['used'] || $parameters['record']['tx_multicolumn_parentid'] !== 0;
    }

}