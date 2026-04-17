<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Component / Community Mapping
    |--------------------------------------------------------------------------
    | Stored values => Arabic display
    */
    'components' => [
        'ALAWITE' => 'علوي',
        'SUNNI' => 'سني',
        'SHIA' => 'شيعي',
        'ISMAILI' => 'إسماعيلي',
        'DRUZE' => 'درزي',
        'MURSHIDI' => 'مرشدي',
        'CHRISTIAN' => 'مسيحي',
        'KURD' => 'كردي',
        'TURKMEN' => 'تركماني',
        'CIRCASSIAN' => 'شركسي',
        'ARMENIAN' => 'أرمني',
        'ASSYRIAN_CHALDEAN' => 'آشوري / كلداني / سرياني',
        'OTHER' => 'أخرى / غير محدد',
    ],

    /*
    |--------------------------------------------------------------------------
    | Violation Types Mapping
    |--------------------------------------------------------------------------
    */
    'violation_types' => [
        'arbitrary_detention'   => 'اعتقال تعسفي',
        'enforced_disappearance'=> 'اختفاء قسري',
        'torture'               => 'تعذيب أو معاملة قاسية',
        'threat'                => 'تهديد أو ترهيب',
        'discrimination'        => 'تمييز ديني أو عرقي',
        'sexual_violence'       => 'عنف جنسي / قائم على النوع',
        'property_violation'    => 'مصادرة أو تدمير ممتلكات',
        'forced_displacement'   => 'تهجير قسري',
        'other'                 => 'أخرى',
    ],

    /*
    |--------------------------------------------------------------------------
    | Entity Policies
    |--------------------------------------------------------------------------
    | Controls what data is allowed per entity
    */
    'entity_policies' => [

        // NGO
        'AMNESTY' => [
            'allow_family_data' => false,
        ],
        'HRW' => [
            'allow_family_data' => false,
        ],

        // Humanitarian
        'ICRC' => [
            'allow_family_data' => true,
        ],
        'UNHCR' => [
            'allow_family_data' => true,
        ],

        // UN Special Procedures
        'UN_SPECIAL_PROCEDURES' => [
            'allow_family_data' => false,
        ],
    ],
];
