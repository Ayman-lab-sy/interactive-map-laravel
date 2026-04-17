<?php

namespace App\Services;

class ReferralDecisionService
{
    /**
     * Analyze case inputs and return referral decision
     *
     * @param array $input
     * @return array
     */
    public function analyze(array $input): array
    {
        $config = config('referral_rules');

        $mandatory  = [];
        $supporting = [];
        $priority   = 'normal';

        // 1) Apply violation rules
        foreach ($input['violations'] ?? [] as $violation) {
            if (!isset($config['violation_rules'][$violation])) {
                continue;
            }

            $rule = $config['violation_rules'][$violation];

            $mandatory  = array_merge($mandatory,  $rule['mandatory']  ?? []);
            $supporting = array_merge($supporting, $rule['supporting'] ?? []);
        }

        // Remove duplicates
        $mandatory  = array_values(array_unique($mandatory));
        $supporting = array_values(array_unique($supporting));

        // 2) Apply boosters
        foreach ($config['boosters'] as $boosterKey => $boosterRule) {
            if (empty($input[$boosterKey])) {
                continue;
            }

            // Add mandatory entities
            if (!empty($boosterRule['add_mandatory'])) {
                $mandatory = array_merge(
                    $mandatory,
                    $boosterRule['add_mandatory']
                );
            }

            // Add supporting entities
            if (!empty($boosterRule['add_supporting'])) {
                $supporting = array_merge(
                    $supporting,
                    $boosterRule['add_supporting']
                );
           }

            // Override priority if defined
            if (!empty($boosterRule['priority'])) {
                $priority = $boosterRule['priority'];
            }
        }

        $mandatory = array_values(array_unique($mandatory));

        $supporting = array_values(array_unique($supporting));

        // 3) Apply limits
        $min = $config['limits']['min_referrals'];
        $max = $config['limits']['max_referrals'];

        $total = count($mandatory) + count($supporting);

        if ($total > $max) {
            $allowedSupporting = max(0, $max - count($mandatory));
            $supporting = array_slice($supporting, 0, $allowedSupporting);
        }

        // Safety: ensure minimum referrals
        if ((count($mandatory) + count($supporting)) < $min) {
            $needed = $min - (count($mandatory) + count($supporting));
            $supporting = array_merge(
                $supporting,
                array_slice(
                    array_diff($config['violation_rules'][array_key_first($config['violation_rules'])]['supporting'] ?? [],
                               $supporting),
                    0,
                    $needed
                )
            );
        }

        return [
            'priority'   => $priority,
            'mandatory'  => array_values(array_unique($mandatory)),
            'supporting' => array_values(array_unique($supporting)),
            'total'      => count(array_unique($mandatory)) + count(array_unique($supporting)),
        ];
    }
}
