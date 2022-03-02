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
                    'No treatment',
                    'Refractive only treatment for hyperopia',
                    'Refractive only treatment for myopia',
                    'Myopia management treatment'
                ];
                $value = $record->meta['query']['b3_'.$prime] ?? 0;
                break;
            case 't6':
                $choices = [
                    'I DO NOT USE the HealthCaringᵀᴹ Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringᵀᴹ Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringᵀᴹ Conversations ISIGHT Model'
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

    public static function items($legend, $prime = 0) {
        /* $legends = [
            "t2" => [0,1,2],
            "t3" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19],
            "t4" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17],
            "t5" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22],
            "t6" => [0,1,2],
            "t7" => [0,1,2,3,4],
            "t8" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13],
            "t9" => [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18],
            "t10" => [0,1,2,3,4,5,6],
            "t11" => [0],
            "t12" => [0]
        ]; */
        $items = Chart::getPrimes();
        if ($prime == 0) {
            return $items[$legend];
        }else {
            return $items[$legend][$prime - 1];
        }
        
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
                    'No treatment',
                    'Refractive only treatment for hyperopia',
                    'Refractive only treatment for myopia',
                    'Myopia management treatment'
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
                    'I DO NOT USE the HealthCaringᵀᴹ Conversations ISIGHT Model',
                    'NO, I DO NOT agree with HealthCaringᵀᴹ Conversations ISIGHT Model',
                    'YES, I agree with HealthCaringᵀᴹ Conversations ISIGHT Model'
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

    public static function getPrimes() {
        $all_items = [
            "t2" => [
                "No treatment",
                "Refractive only treatment for hyperopia",
                "Refractive only treatment for myopia",
                "Myopia management treatment"
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
                "Software’s accuracy in getting the right product for patient from 1st order",
                "Abiliti 1-Day therapeutic lens"
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
                "Share Your Abiliti donation with patient purchase to Sight for Kids",
                "Abiliti 1-Day therapeutic lens",
            ],
            "t5" => [
                "MiSight",
                "naturalVue",
                "Paragon CRT",
                "Abiliti Overnight",
                'Euclid Emerald',
                "B&L MOONLENS",
                'Wave NighLens',
                'iSee',
                'Zeiss MyoVision',
                'MiyoSmart (Hoya)',
                'Multifocal/PALs Spectacles',
                'Atropine',
                'Menicon Bloom Day',
                'Menicon Bloom Night',
                'Stellest (Essilor)',
                'Myopilux lenses (Essilor)',
                'E-Lens (E&E)',
                'DK4 (Oculus) ',
                'Breath O Correct (Seed)',
                'DreamLite (ProCornea)',
                'Menicon Z Night',
                'Off label multifocal soft Contacts',
                'DISC'
            ],
            "t6" => [
                "I DO NOT USE the HealthCaringᵀᴹ Conversations ISIGHT Model",
                "NO, I DO NOT agree with HealthCaringᵀᴹ  Conversations ISIGHT Model",
                "YES, I agree with HealthCaringᵀᴹ Conversations ISIGHT Model"
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
                "Use the word “recommend” when talking about the treatment option (i.e. I am “recommending” product “X” to treat your child’s myopia)"
            ],
            "t10" => [
                "The Healthcaring™Conversations Conversation Starter: filled out by the parent before entering the appointment room with the ECP. ​It identifies what matters to the parent and why; connects their values to treatment decisions and behaviors.​",
                "The patient myopia action plan:  helps providers and staff layout a plan (behavioral and treatment goals/plan) for the parent and child to follow when they leave the office.",
                "SeeAbiliti App: gives parents tools and resources to monitor child’s activity (outdoor time), follow the treatment plan, and manage upcoming appts with ECPs.",
                "New wearer kit: New wearer kits to be given to patients to help them start their Abiliti™ journey and properly care for their lenses.​",
                "Lifestyle Chart:  Used as worksheet between parent and ECP to determine treatment options that best-suit the patients’ lifestyle​",
                "Tools (e.g., brochure(s), posters, etc.) that educate parents on the condition (myopia)",
                "Tools (e.g. brochure(s), posters, etc.) that educate parents on Abiliti"
            ],
            "t11" => [
                "Please indicate what you like about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:"
            ],
            "t12" => [
                "Please indicate what you dislike about Johnson & Johnson Vision’s approach to myopia management and the Abiliti™ brand:"
            ]
        ];

        /* $items = [];
        foreach ($legends as $legend => $primes) {
            $items[$legend] = [];
            foreach ($primes as $prime) {
                $items[$legend][] = $all_items[$legend][$prime];
            }
        } */
        return $all_items;
    }
}
