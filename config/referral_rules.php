<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Referral Decision Rules
    |--------------------------------------------------------------------------
    | هذا الملف يحتوي فقط على قواعد القرار
    | بدون أي منطق تنفيذي أو Controllers
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | 1. Violation Rules
    |--------------------------------------------------------------------------
    | كل نوع انتهاك ↔ الجهات المناسبة له
    |--------------------------------------------------------------------------
    */

    'violation_rules' => [

        'extrajudicial_killing' => [
            'mandatory' => [
                'UN Special Rapporteur – Extrajudicial, Summary or Arbitrary Executions',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'torture' => [
            'mandatory' => [
                'UN Special Rapporteur – Torture',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
                'ICRC',
            ],
        ],

        'enforced_disappearance' => [
            'mandatory' => [
                'UN Working Group on Enforced or Involuntary Disappearances',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'arbitrary_detention' => [
            'mandatory' => [
                'UN Working Group on Arbitrary Detention',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'freedom_expression' => [
            'mandatory' => [
                'UN Special Rapporteur – Freedom of Opinion and Expression',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'human_rights_defenders' => [
            'mandatory' => [
                'UN Special Rapporteur – Human Rights Defenders',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'violence_against_women' => [
            'mandatory' => [
                'UN Special Rapporteur – Violence against Women and Girls, its Causes and Consequences',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'minority_issues' => [
            'mandatory' => [
                'UN Special Rapporteur on Minority Issues',
                'UN Special Rapporteur on freedom of religion or belief',
                'OHCHR',
            ],
            'supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
        ],

        'refugee_protection' => [
            'mandatory' => [
                'UNHCR',
            ],
            'supporting' => [
                'OHCHR',
                'Amnesty International',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 2. Boosters
    |--------------------------------------------------------------------------
    | عوامل ترجيح لا تُنشئ قرارًا جديدًا
    | بل تُعزّز القرار القائم
    |--------------------------------------------------------------------------
    */

    'boosters' => [

        'urgent_risk' => [
            'priority' => 'urgent',
            'add_mandatory' => [
                'OHCHR',
            ],
        ],

        'victim_deceased' => [
            'add_mandatory' => [
                'UN Special Rapporteur – Extrajudicial, Summary or Arbitrary Executions',
                'OHCHR',
            ],
            'priority' => 'high',
        ],

        'multiple_violations' => [
            'add_supporting' => [
                'Amnesty International',
                'Human Rights Watch',
            ],
            'priority' => 'high',
        ],

        'minority' => [
            'add_mandatory' => [
                'UN Special Rapporteur on Minority Issues',
                'OHCHR',
            ],
        ],

        'refugee' => [
            'add_mandatory' => [
                'UNHCR',
            ],
            'priority' => 'high',
        ],

        'state_actor' => [
            'add_mandatory' => [
                'OHCHR',
            ],
            'priority' => 'high',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | 3. Limits
    |--------------------------------------------------------------------------
    | ضبط عدد الإحالات
    |--------------------------------------------------------------------------
    */

    'limits' => [

        'min_referrals' => 2,
        'max_referrals' => 5,

        'exception_max' => 7,

        'exception_conditions' => [
            'urgent_risk',
            'multiple_violations',
            'minority',
            'victim_deceased',
            'state_actor',
        ],
    ],

];
