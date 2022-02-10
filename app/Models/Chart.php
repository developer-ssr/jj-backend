<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'series' => 'array',
        'categories' => 'array'
    ];

    public static function getExpData($legend, $record, $prime) 
    {
        $choices = [];
        switch ($legend) {
            case 't2':
                $choices = [
                    'No treatment recommended',
                    'Refractive only treatment: you fit only with single vision solutions (glasses or contact lenses)',
                    'Myopia management treatment: you fit with myopia management treatments (Ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, atropine)'
                ];
                $value = $record->meta['query']['b3_'.$prime];
                break;
            case 't6':
                $choices = [
                    'I DO NOT USE the HealthCaringTM Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringTM Conversations ISIGHT Model'
                ];
                $value = $record->data[$legend];
                break;
            case 't7':
                $choices = [
                    'None',
                    'About 25%',
                    'About 50%',
                    'About 75%',
                    'Virtually all of my patients'
                ];
                $value = $record->data[$legend];
                break;
            case 't11':
                $choices = [
                    'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                $value = 1;//$record->meta['query']['d1'];
                break;
            case 't12':
                $choices = [
                    'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                $value = 1;//$record->meta['query']['d2'];
                break;
            default:
                break;
        }
        $index = $prime - 1;
        $tmp_data = [
            'index' => $prime,
            'prime' => $choices[$index],
            'equivalent' => $choices[$index],
            'data' => [
                [
                    "target" => '',
                    "equivalent" => '',
                    "index" => $prime,
                    "value" => $legend == 't2' ? $value: 1,//$prime,
                    "selected" => $legend == 't2' ? true : ($value == $prime ? true: false)
                ]
            ],
        ]; 
        return $tmp_data;
    }

    public static function getCountry($code) {
        switch ($code) {
            case 'us':
                $country = 'USA';
                break;
            case 'sg':
                $country = 'Singapore';
                break;
            case 'hk':
                $country = 'Hongkong';
                break;
            case 'ca':
                $country = 'Canada';
                break;
            default:
                $country = '';
                break;
        }
        return $country;
    }

    public static function getInfo($series) {
        $dimensions = [
            'Satisfaction' => [],
            'Value' => [],
            'HC2 Behavior freq.' => [],
            'HC2 Content' => [],
            'Tool use/reco' => []
        ];
        $items = $this->items();

        foreach ($series as $value) {
            $tmp = Str::of($value['name'])->explode('_');
            $t = Str::lower($tmp[0]);//t3
            $prime = $tmp[1];
            switch ($t) {
                case 't3':
                case 't4':
                case 't8':
                case 't9':
                case 't10':
                    $dimensions['Satisfaction'] = [
                        'enable' => false,
                        'items' => $this->items($t)
                    ];
                    
                    break;
                default:
                    # go to next loop
                    break;
            }
        }
        return [
            'dimensions' => $dimensions,
            'items' => $items
        ];
    }

    public static function items($legend, $prime = 0) {
        $items = [
            "t2" => [
                "No treatment recommended",
                "Refractive only treatment: you fit only with single vision solutions (glasses or contact lenses)",
                "Myopia management treatment: you fit with myopia management treatments (Ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, atropine)"
            ],
            "t3" => [
                "Customer service",
                "HealthCaring Conversations – The ISIGHT Model",
                "Training on the fitting process",
                "Fitting guides",
                "FitAbiliti fitting Software",
                "The SeeAbiliti App",
                "Product ordering",
                "The Clinical Sales Consultant: Johnson & Johnson Vision Myopia Subject Matter Expert",
                "Helping me provide personalized care to my myopia management patients",
                "Abiliti Overnight therapeutic lens",
                "Abiliti New Wearer Kit",
                "On-line tools/programs that help me train myself and/or my staff",
                "In Person tools/programs that help me train myself and/or my staff",
                "Tools/programs that educate me on the disease (myopia) and the product science",
                "Patient brochure(s) that educate the parent on the condition (myopia)",
                "Patient brochure(s) that educate the parent on the brand (Abiliti)",
                "Master Your Abiliti Training Program",
                "Share Your Abiliti donation with patient purchase to Sight for Kids",
                "Turnaround time from product ordering to delivery",
                "Software’s accuracy in getting the right product for patient from 1st order"
            ],
            "t4" => [
                "Customer service",
                "HealthCaring Conversations – The ISIGHT Model",
                "Training on the fitting process",
                "Fitting guides",
                "FitAbiliti fitting Software",
                "The SeeAbiliti App",
                "Product ordering",
                "The Clinical Sales Consultant: Johnson & Johnson Vision Myopia Subject Matter Expert",
                "Helping me provide personalized care to my myopia management patients",
                "Abiliti Overnight Therapeutic lens",
                "Abiliti New Wearer Kit",
                "On-line tools/programs that help me train myself and/or my staff",
                "In Person tools/programs that help me train myself and/or my staff",
                "Tools/programs that educate me on the disease (myopia) and the product science",
                "Patient brochure(s) that educate the parent on the condition (myopia)",
                "Patient brochure(s) that educate the parent on the brand (Abiliti)",
                "Master Your Abiliti Training Program",
                "Share Your Abiliti donation with patient purchase to Sight for Kids"
            ],
            "t5" => [
                "MiSight",
                "naturalVue",
                "Paragon CRT",
                "Abiliti Overnight",
                'Euclid Emerald',
                "MOONLENS",
                'Wave NighLens',
                'Abiliti 1-Day',
                'Menicon',
                'GP Specialists'
            ],
            "t6" => [
                "I DO NOT USE the HealthCaringTM Conversations ISIGHT Model",
                "NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model",
                "YES, I agree with HealthCaringTM Conversations ISIGHT Model"
            ],
            "t7" => [
                "None",
                "About 25%",
                "About 50%",
                "About 75%",
                "Virtually all of my patients"
            ],
            "t8" => [
                "Assess parents’ values and motivations for their child before providing myopia management recommendations.",
                "Assess a child’s current eye health in relation to their existing treatments, behaviors, and challenges they’re experiencing.",
                "Discuss the child’s eye health and eye care practices using easy to understand terms.",
                "Confirm the parent’s understanding of their child’s eye health and eye care practices.",
                "Discuss personally relevant and specific recommendations to improve the child’s behaviors and outcomes relative to their eye health.",
                "Discuss myopia management recommendations in relation to the parents’ values and motivations.",
                "Discuss parent/child questions and concerns before agreeing on next steps to manage their myopia.",
                "Collaborate with the parent/child to determine achievable goals regarding myopia progression and next steps.",
                "Discuss barriers which might prevent the parent/child from achieving goals or taking action.",
                "Assist the parent/child with strategies to overcome barriers.",
                "When possible, show the parent/child how to perform eye health behaviors versus using written or verbal instruction only.",
                "Help the parent/child create an action plan around their goals and agreed upon recommendations.",
                "Provide the parent/child with relevant feedback and support during myopia treatment.",
                "Follow up with the parent/child throughout their myopia management plan."
            ],
            "t9" => [
                "The long-term risk of potential visual impairment due to a variety of eye diseases (i.e. retinal detachment, macular degeneration)",
                "That these eye diseases present later in life (myopia is a silent epidemic)",
                "That myopia is chronic",
                "That myopia is progressive or gets worse over time",
                "That myopia is a disease",
                "The need to monitor the length of the eye",
                "The progression of myopia can be slowed with treatment",
                "Myopia cannot be cured or reversed",
                "The potential of avoiding thicker glasses",
                "Avoid using words like “blindness”",
                "The potential of avoiding a worsening prescription over time",
                "The potential to reduce a child’s future prescription as an adult",
                "The potential to reduce the chances of developing high myopia (-5.00D or more)",
                "Spending more time outdoors can be helpful when managing myopia",
                "Reducing time spent in near work can be helpful when managing myopia",
                "Genetics is a risk factor for myopia",
                "Lifestyle factors like lack of outdoor activities and extensive near work are risk factors for myopia",
                "Use the word “prescribe” when talking about the treatment option (i.e. I am “prescribing” product “X” to treat your child’s myopia)",
                "Use the word “does not recommend” when talking about the treatment option (i.e. I am “not recommending” product “X” to treat your child’s myopia)"
            ],
            "t10" => [
                "The Healthcaring™Conversations Conversation Starter: filled out by the parent before entering the appointment room with the ECP. ​It identifies what matters to the parent and why; connects their values to treatment decisions and behaviors.​",
                "The patient myopia action plan:  helps providers and staff layout a plan (behavioral and treatment goals/plan) for the parent and child to follow when they leave the office.",
                "SeeAbiliti App: gives parents tools and resources to monitor child’s activity (outdoor time), follow the treatment plan, and manage upcoming appts with ECPs.",
                "New wearer kit: New wearer kits to be given to patients to help them start their Abiliti™ journey and properly care for their lenses.​",
                "Lifestyle Chart:  Used as worksheet between parent and ECP to determine treatment options that best-suit the patients’ lifestyle​",
                "Tools (e.g., brochure(s), posters, etc.) that educate parents on the condition (myopia)",
                "Tools (e.g. brochure(s), posters, etc.) that educate parents on the Abiliti"
            ],
            "t11" => [
                "Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:"
            ],
            "t12" => [
                "Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:"
            ]
        ];
        if ($prime == 0) {
            return $items[$legend];
        }else {
            return $items[$legend][$prime - 1];
        }
        
    }
    public static function items_old() {
        return [
                [
                    'num' => 1,
                    'description' => "Customer service",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 2,
                    'description' => "HealthCaring Conversations – The ISIGHT Model",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 3,
                    'description' => "Training on the fitting process",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 4,
                    'description' => "Fitting guides",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 5,
                    'description' => "FitAbiliti fitting Software",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 6,
                    'description' => "The SeeAbiliti App",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 7,
                    'description' => "Product ordering",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 8,
                    'description' => "The Clinical Sales Consultant: Johnson & Johnson Vision Myopia Subject Matter Expert",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 9,
                    'description' => "Helping me provide personalized care to my myopia management patients",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 10,
                    'description' => "Abiliti Overnight therapeutic lens",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 11,
                    'description' => "Abiliti New Wearer Kit",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 12,
                    'description' => "On-line tools/programs that help me train myself and/or my staff",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 13,
                    'description' => "In Person tools/programs that help me train myself and/or my staff",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 14,
                    'description' => "Tools/programs that educate me on the disease (myopia) and the product science",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 15,
                    'description' => "Patient brochure(s) that educate the parent on the condition (myopia)",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 16,
                    'description' => "Patient brochure(s) that educate the parent on the brand (Abiliti)",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 17,
                    'description' => "Master Your Abiliti Training Program",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 18,
                    'description' => "Share Your Abiliti donation with patient purchase to Sight for Kids",
                    'ts' => ['t3', 't4']
                ],
                [
                    'num' => 19,
                    'description' => "Turnaround time from product ordering to delivery",
                    'ts' => ['t3']
                ],
                [
                    'num' => 20,
                    'description' => "Software’s accuracy in getting the right product for patient from 1st order",
                    'ts' => ['t3']
                ],
                [
                    'num' => 21,
                    'description' => "Assess parents’ values and motivations for their child before providing myopia management recommendations.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 22,
                    'description' => "Assess a child’s current eye health in relation to their existing treatments, behaviors, and challenges they’re experiencing.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 23,
                    'description' => "Discuss the child’s eye health and eye care practices using easy to understand terms.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 24,
                    'description' => "Confirm the parent’s understanding of their child’s eye health and eye care practices.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 25,
                    'description' => "Discuss personally relevant and specific recommendations to improve the child’s behaviors and outcomes relative to their eye health.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 26,
                    'description' => "Discuss myopia management recommendations in relation to the parents’ values and motivations.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 27,
                    'description' => "Discuss parent/child questions and concerns before agreeing on next steps to manage their myopia.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 28,
                    'description' => "Collaborate with the parent/child to determine achievable goals regarding myopia progression and next steps.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 29,
                    'description' => "Discuss barriers which might prevent the parent/child from achieving goals or taking action.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 30,
                    'description' => "Assist the parent/child with strategies to overcome barriers.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 31,
                    'description' => "When possible, show the parent/child how to perform eye health behaviors versus using written or verbal instruction only.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 32,
                    'description' => "Help the parent/child create an action plan around their goals and agreed upon recommendations.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 33,
                    'description' => "Provide the parent/child with relevant feedback and support during myopia treatment.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 34,
                    'description' => "Follow up with the parent/child throughout their myopia management plan.",
                    'ts' => ['t8']
                ],
                [
                    'num' => 35,
                    'description' => "The long-term risk of potential visual impairment due to a variety of eye diseases (i.e. retinal detachment, macular degeneration)",
                    'ts' => ['t9']
                ],
                [
                    'num' => 36,
                    'description' => "That these eye diseases present later in life (myopia is a silent epidemic)",
                    'ts' => ['t9']
                ],
                [
                    'num' => 37,
                    'description' => "That myopia is chronic",
                    'ts' => ['t9']
                ],
                [
                    'num' => 38,
                    'description' => "That myopia is progressive or gets worse over time",
                    'ts' => ['t9']
                ],
                [
                    'num' => 39,
                    'description' => "That myopia is a disease",
                    'ts' => ['t9']
                ],
                [
                    'num' => 40,
                    'description' => "The need to monitor the length of the eye",
                    'ts' => ['t9']
                ],
                [
                    'num' => 41,
                    'description' => "The progression of myopia can be slowed with treatment",
                    'ts' => ['t9']
                ],
                [
                    'num' => 42,
                    'description' => "Myopia cannot be cured or reversed",
                    'ts' => ['t9']
                ],
                [
                    'num' => 43,
                    'description' => "The potential of avoiding thicker glasses",
                    'ts' => ['t9']
                ],
                [
                    'num' => 44,
                    'description' => "Avoid using words like “blindness”",
                    'ts' => ['t9']
                ],
                [
                    'num' => 45,
                    'description' => "The potential of avoiding a worsening prescription over time",
                    'ts' => ['t9']
                ],
                [
                    'num' => 46,
                    'description' => "The potential to reduce a child’s future prescription as an adult",
                    'ts' => ['t9']
                ],
                [
                    'num' => 47,
                    'description' => "The potential to reduce the chances of developing high myopia (-5.00D or more)",
                    'ts' => ['t9']
                ],
                [
                    'num' => 48,
                    'description' => "Spending more time outdoors can be helpful when managing myopia",
                    'ts' => ['t9']
                ],
                [
                    'num' => 49,
                    'description' => "Reducing time spent in near work can be helpful when managing myopia",
                    'ts' => ['t9']
                ],
                [
                    'num' => 50,
                    'description' => "Genetics is a risk factor for myopia",
                    'ts' => ['t9']
                ],
                [
                    'num' => 51,
                    'description' => "Lifestyle factors like lack of outdoor activities and extensive near work are risk factors for myopia",
                    'ts' => ['t9']
                ],
                [
                    'num' => 52,
                    'description' => "Use the word “prescribe” when talking about the treatment option (i.e. I am “prescribing” product “X” to treat your child’s myopia)",
                    'ts' => ['t9']
                ],
                [
                    'num' => 53,
                    'description' => "Use the word “recommend” when talking about the treatment option (i.e. I am “recommending” product “X” to treat your child’s myopia)",
                    'ts' => ['t9']
                ],
                [
                    'num' => 54,
                    'description' => "The Healthcaring™Conversations Conversation Starter: filled out by the parent before entering the appointment room with the ECP. ​It identifies what matters to the parent and why; connects their values to treatment decisions and behaviors.",
                    'ts' => ['t10']
                ],
                [
                    'num' => 55,
                    'description' => "The patient myopia action plan:  helps providers and staff layout a plan (behavioral and treatment goals/plan) for the parent and child to follow when they leave the office.",
                    'ts' => ['t10']
                ],
                [
                    'num' => 56,
                    'description' => "SeeAbiliti App: gives parents tools and resources to monitor child’s activity (outdoor time), follow the treatment plan, and manage upcoming appts with ECPs.",
                    'ts' => ['t10']
                ],
                [
                    'num' => 57,
                    'description' => "New wearer kit: New wearer kits to be given to patients to help them start their Abiliti™ journey and properly care for their lenses.",
                    'ts' => ['t10']
                ],
                [
                    'num' => 58,
                    'description' => "Lifestyle Chart:  Used as worksheet between parent and ECP to determine treatment options that best-suit the patients’ lifestyle",
                    'ts' => ['t10']
                ],
                [
                    'num' => 59,
                    'description' => "Tools (e.g., brochure(s), posters, etc.) that educate parents on the condition (myopia)",
                    'ts' => ['t10']
                ],
                [
                    'num' => 60,
                    'description' => "Tools (e.g. brochure(s), posters, etc.) that educate parents on the Abiliti",
                    'ts' => ['t10']
                ]
            ];
    }

    public static function getQuestion($legend) 
    {
        $choices = [];
        switch ($legend) {
            case 't2':
                $dimension = '';
                $question = 'For your patients between 5-18 years of age, please estimate the percent of patients that fall within each category.
                The total must sum to 100%';
                $choices = [
                    'No treatment recommended',
                    'Refractive only treatment: you fit only with single vision solutions (glasses or contact lenses)',
                    'Myopia management treatment: you fit with myopia management treatments (Ortho-K, multifocal soft contacts or glasses, myopia control soft contacts or glasses, atropine)'
                ];
                break;
            case 't3':
                $dimension = 'Satisfaction';
                $question = 'Please indicate your satisfaction level with Abiliti in the following areas:';
                $choices = [
                    'Not at all satisfied',
                    'Not very satisfied',
                    'Somewhat satisfied',
                    'Very satisfied',
                    'Extremely satisfied'
                ];
                break;
            case 't4':
                $dimension = 'Value';
                $question = 'Please indicate which of the following areas from Abiliti are effective and providing value to your myopia management practice.';
                $choices = [
                    'No',
                    'Yes'
                ];
                break;
            case 't5':
                $dimension = 'Brand';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                $choices = [
                    'Definitely would not recommend',
                    'Probably would not recommend',
                    'Might or might not recommend',
                    'Probably would recommend',
                    'Definitely would recommend'
                ];
                break;
            case 't6':
                $dimension = '';
                $question = 'Now we are going to ask you a little about the conversations you may have with the parents of your patients between 5-18 years. Please indicate if you agree with the following statement:  Abiliti HealthCaring Conversations, ISIGHT Model, provides me with usable strategies for engaging in high quality conversations with my patients.';
                $choices = [
                    'I DO NOT USE the HealthCaringTM Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringTM Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringTM Conversations ISIGHT Model'
                ];
                break;
            case 't7':
                $dimension = '';
                $question = 'For your patients between 5-18 years of age, please estimate the percent of parents/patients you approach to discuss myopia management as a treatment option during the first visit.';
                $choices = [
                    'None',
                    'About 25%',
                    'About 50%',
                    'About 75%',
                    'Virtually all of my patients'
                ];
                break;
            case 't8':
                $dimension = 'HC2 Behavior freq.';
                $question = 'In general, how frequently are you able to do the following with your myopia patients and/or their parents?';
                $choices = [
                    'Never',
                    'Rarely',
                    'Sometimes',
                    'Almost always',
                    'Always'
                ];
                break;
            case 't9':
                $dimension = 'HC2 Content';
                $question = 'As part of the parent conversation, please indicate if you include the following information or language in your discussion:';
                $choices = [
                    'No',
                    'Yes'
                ];
                break;
            case 't10':
                $dimension = 'Tool use/reco';
                $question = 'Please indicate how often you use/recommend the following tools from Abiliti™:';
                $choices = [
                    'Do not use or recommend',
                    'Occasionally use or recommend',
                    'Sometimes use or recommend',
                    'Always use or recommend'
                ];
                break;
            case 't11':
                $dimension = '';
                $question = 'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:';
                $choices = [
                    'Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                break;
            case 't12':
                $dimension = '';
                $question = 'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:';
                $choices = [
                    'Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:'
                ];
                break;
            default:
                $dimension = 'Dimension';
                $question = 'How likely would you be to recommend the following to your patients and their parents?';
                break;
        }
        return ['question' => $question, 'dimension' => $dimension, 'choices' => $choices];
    }
}
