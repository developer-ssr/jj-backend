<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\Office;
use Illuminate\Http\Request;

class FilterController extends Controller
{

    public function primes(Office $office)
    {
        /* 124: 'ca',
        344: 'hk',
        702: 'sg',
        840: 'us' */
        switch ($office->code) {
            case 344: //hk
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
                        'Abiliti 1-Day'
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
                        "Use the word “recommend” when talking about the treatment option (i.e. I am “recommending” product “X” to treat your child’s myopia)"
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
                break;
            
            default:
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
                        "Use the word “recommend” when talking about the treatment option (i.e. I am “recommending” product “X” to treat your child’s myopia)"
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
                break;
        }
        
        return response()->json($items);
    }

    public function index(Request $request, Office $office)
    {
        $filters = Filter::where('user_id', $request->user()->id)->where('office_id', $office->id)->get();
        return response()->json($filters);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'office_id' => 'required',
            'data' => 'required',
        ]);
        $filter = Filter::create([
            'name' => $request->name,
            'data' => $request->data,
            'office_id' => $request->office_id,
            'user_id' => $request->user()->id
        ]);
        return response()->json($filter);
    }

    public function update(Request $request, Filter $filter)
    {
        $request->validate([
            'name' => 'required',
            'office_id' => 'required',
            'data' => 'required'
        ]);
        $filter->update([
            'name' => $request->name,
            'data' => $request->data,
            'office_id' => $request->office_id
        ]);
        return response()->json($filter, 200);
    }

    public function destroy(Filter $filter)
    {
        $filter->delete();
        return response('ok');
    }
}
