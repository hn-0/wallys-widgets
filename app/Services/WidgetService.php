<?php

namespace App\Services;

class WidgetService
{
    private static array $defaultPackQuantities = [5000, 2000, 1000, 500, 250];

    /**
     * Returns only the pack quantities which are smaller than the order quantity AND the first pack quantity which is larger.
     *
     * @param  int  $orderQuantity
     * @param  array  $packQuantities
     *
     * @return int[]
     */
    private static function trimIrrelevantPackQuantities(int $orderQuantity, array $packQuantities): array
    {
        // A default of -1 will return the last (smallest) element
        $relevantOffset = 0;

        foreach ($packQuantities as $packQuantityKey => $packQuantity) {
            // Return the pack if it's a perfect match
            if ($packQuantity === $orderQuantity) {
                return [$packQuantity];
            }

            // Stop increasing the offset if it's the first pack quantity below the order quantity
            if ($packQuantity <= $orderQuantity) {
                break;
            }

            $relevantOffset = $packQuantityKey;
        }

        // Keep only pack quantities which are smaller than the order quantity, and the first one which is larger
        return array_slice($packQuantities, $relevantOffset);
    }

    /**
     * Returns a combination of packs which will satisfy the amount of widgets ordered whilst keeping excess widgets and pack count to a minimum.
     *
     * @param  int  $widgetsRequired
     * @param  int[]  $packQuantities
     * @param  array  $cached
     *
     * @return array Array with keys: 'excessWidgets', 'packs'.
     */
    private static function getMinimumPacksRequiredRecursive(int $widgetsRequired, array $packQuantities, array &$cached = []): array
    {
        // Trim pack quantities not worth calculating
        $packQuantities = self::trimIrrelevantPackQuantities($widgetsRequired, $packQuantities);

        $bestExcessWidgets = null;
        $bestPackCombination = [];

        foreach ($packQuantities as $packQuantity) {
            $widgetsRemaining = $widgetsRequired - $packQuantity;

            // Perfect solution, return it
            if ($widgetsRemaining === 0) {
                return [
                    'excessWidgets' => 0,
                    'packs' => [$packQuantity],
                ];
            }

            // Acceptable solution, store (if fewer leftover widgets than the previous best) and try the next one
            if ($widgetsRemaining < 0) {
                if (null === $bestExcessWidgets || $widgetsRemaining > $bestExcessWidgets) {
                    $bestExcessWidgets = $widgetsRemaining;
                    $bestPackCombination = [$packQuantity];
                }

                continue;
            }
            // Insufficient solution below

            // If a solution exists already, trim all packs larger than it (as they will never be better)
            if (!empty($bestPackCombination)) {
                $packQuantities = array_filter($packQuantities, function($packQuantity) use ($bestPackCombination) {
                    return $packQuantity <= $bestPackCombination[0];
                });
            }

            // Calculate the best solution for the remaining widgets if it hasn't been done already
            if (!array_key_exists($widgetsRemaining, $cached)) {
                $innerBest = self::getMinimumPacksRequiredRecursive($widgetsRemaining, $packQuantities, $cached);
                $cached[$widgetsRemaining] = [$innerBest['excessWidgets'], $innerBest['packs']];
            }

            // Retrieve the best solution for the remaining widgets
            [$innerExcessWidgets, $innerPacks] = $cached[$widgetsRemaining];

            // Perfect solution, return it
            if ($innerExcessWidgets === 0) {
                return [
                    'excessWidgets' => 0,
                    'packs' => array_merge($innerPacks, [$packQuantity]),
                ];
            }

            // If the solution for the remaining widgets is closer to the goal, store it
            if (null !== $innerExcessWidgets && (null === $bestExcessWidgets || $innerExcessWidgets > $bestExcessWidgets)) {
                $bestExcessWidgets = $innerExcessWidgets;
                $bestPackCombination = array_merge($innerPacks, [$packQuantity]);
            }
        }

        return [
            'excessWidgets' => $bestExcessWidgets,
            'packs' => $bestPackCombination,
        ];
    }

    /**
     * Returns a combination of packs which will satisfy the amount of widgets ordered whilst keeping excess widgets and pack count to a minimum.
     * Results are ordered from largest to smallest by pack size.
     *
     * @param  int  $widgetsOrdered
     *
     * @return array Array where keys are the pack size and values are the quantity of that pack.
     */
    public static function getMinimumPacksRequired(int $widgetsOrdered): array
    {
        return array_reverse(array_count_values(self::getMinimumPacksRequiredRecursive($widgetsOrdered, self::$defaultPackQuantities)['packs']), true);
    }
}
