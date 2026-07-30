<?php
/**
 * EnPharChem - PetroSA GTL Problem Statement content
 * --------------------------------------------------
 * Editorial content for the customer-facing problem-statement document aimed at
 * PetroSA's gas-to-liquids refinery at Mossel Bay. For each of the platform's
 * 15 module categories it states the operating problem in GTL terms, gives
 * worked use cases, and says how to perform and how to analyse each one.
 *
 * Consumed by PetroSAProblemStatementPdfBuilder and the HTML edition.
 *
 * SCOPE NOTE: one problem statement per module *category* (the 15 modules the
 * platform presents in its navigation), naming the constituent modules used in
 * each. A statement per individual module (119 of them) would be shallow and
 * repetitive; the category is the level at which an engineering problem is
 * actually owned.
 *
 * INTEGRITY NOTE: this is a vendor-authored solution-fit document. It is not
 * endorsed by, commissioned by, or produced with PetroSA. Plant context is
 * drawn from publicly reported information and is deliberately described in
 * general terms. Every numeric target in the analysis sections is an
 * ILLUSTRATIVE placeholder to be replaced with verified plant data before the
 * document is used to support any commitment. See disclaimer().
 */

class PetroSAProblemStatement
{
    const CLIENT   = 'PetroSA';
    const ASSET    = 'Mossel Bay gas-to-liquids refinery';
    const DOCTITLE = 'GTL Refinery Problem Statement & Use Case Handbook';

    /** Opening context for the asset and the commercial situation. */
    public static function context()
    {
        return [
            'headline' => 'Sustaining and optimising a gas-to-liquids refinery under feedstock, energy and grid constraints',
            'paragraphs' => [
                'PetroSA, a subsidiary of South Africa\'s Central Energy Fund, operates the gas-to-liquids '
                . 'refinery at Mossel Bay in the Western Cape - one of a small number of commercial GTL '
                . 'facilities worldwide. The plant converts natural gas and condensate into synthetic fuels '
                . 'through reforming to synthesis gas, Fischer-Tropsch synthesis, and downstream product '
                . 'work-up into naphtha, kerosene, diesel and LPG-range products.',

                'The facility faces a compounding set of engineering problems that are structural rather '
                . 'than incidental. Feed gas from the offshore fields that the plant was built around has '
                . 'declined, which puts the front end and the whole product slate off its design point and '
                . 'makes feedstock substitution - imported LNG, additional gas development, or co-feeds - a '
                . 'precondition for sustained operation. GTL is intrinsically energy-intensive, so thermal '
                . 'efficiency and carbon intensity dominate operating margin. The asset base is mature and '
                . 'has spent periods at reduced rates, raising integrity and reliability risk on rotating '
                . 'equipment and fired plant. And because the site sits behind a national grid subject to '
                . 'load curtailment, electrical resilience is an availability problem, not just a cost one.',

                'None of these can be answered by judgement alone. Each requires a calibrated model, a '
                . 'reconciled measurement base, and an explicit statement of what "better" means before an '
                . 'engineer changes anything. That is the purpose of this handbook: for every module '
                . 'category in the EnPharChem platform, it states the problem in the language of this '
                . 'refinery, then gives use cases with the steps to perform them and the criteria to '
                . 'analyse the result against.',
            ],
            'chain' => self::valueChain(),
        ];
    }

    /** The GTL value chain, used to anchor each module category to a plant area. */
    public static function valueChain()
    {
        return [
            ['stage' => 'Feed gas reception',   'detail' => 'Offshore pipeline gas, condensate and/or LNG regasification; metering and composition control.'],
            ['stage' => 'Gas conditioning',     'detail' => 'Acid gas removal (CO2/H2S), dehydration, mercury and sulfur guard beds to protect catalyst.'],
            ['stage' => 'Syngas generation',    'detail' => 'Reforming (steam/autothermal/partial oxidation) to a H2:CO ratio suited to the synthesis step; oxygen supply where required.'],
            ['stage' => 'Fischer-Tropsch',      'detail' => 'Catalytic synthesis of hydrocarbons from syngas, with large exothermic heat removal and reaction-water production.'],
            ['stage' => 'Product work-up',      'detail' => 'Separation, oligomerisation, hydroprocessing and fractionation into the finished product slate.'],
            ['stage' => 'Utilities',            'detail' => 'Steam, power, cooling water, fuel gas, hydrogen and oxygen - the dominant lever on thermal efficiency.'],
            ['stage' => 'Water treatment',      'detail' => 'Recovery and treatment of oxygenate-bearing Fischer-Tropsch reaction water.'],
            ['stage' => 'Tankage & dispatch',   'detail' => 'Blending to national fuel specifications, tank management, and road, rail or marine dispatch.'],
        ];
    }

    /** The macro problems, each mapped to the module categories that address it. */
    public static function coreProblems()
    {
        return [
            [
                'title'   => 'Feedstock decline and substitution',
                'detail'  => 'Declining offshore gas puts the front end off design and makes the economics of substitute feed (LNG import, further gas development) the central planning question.',
                'answered'=> 'Subsurface Science; Concurrent FEED; Process Simulation for Energy; Petroleum Supply Chain',
            ],
            [
                'title'   => 'Thermal efficiency and carbon intensity',
                'detail'  => 'Reforming and synthesis dominate energy use; every point of thermal efficiency moves both margin and emissions.',
                'answered'=> 'Energy & Utilities Optimization; Exchanger Design & Rating; Dynamic Optimization',
            ],
            [
                'title'   => 'Product slate and specification compliance',
                'detail'  => 'Synthetic product streams must be blended to national fuel specifications with minimum quality giveaway as catalyst selectivity shifts.',
                'answered'=> 'Process Simulation for Chemicals; Petroleum Supply Chain; Advanced Process Control',
            ],
            [
                'title'   => 'Asset integrity and reliability',
                'detail'  => 'Mature rotating and fired equipment, periods at reduced rate, and long lead times make unplanned downtime the largest single production risk.',
                'answered'=> 'Asset Performance Management; Concurrent FEED (availability modelling); Operations Support',
            ],
            [
                'title'   => 'Electrical resilience',
                'detail'  => 'Exposure to national grid curtailment threatens continuous operation of a plant that cannot be cycled cheaply.',
                'answered'=> 'Digital Grid Management; Energy & Utilities Optimization',
            ],
            [
                'title'   => 'Measurement trust and data fragmentation',
                'detail'  => 'Unreconciled measurements and siloed historian, laboratory and maintenance data mean every study starts by rebuilding its own data set.',
                'answered'=> 'Industrial Data Fabric; Manufacturing Execution Systems; Operations Support',
            ],
        ];
    }

    /** Explicit statement of what this document is and is not. */
    public static function disclaimer()
    {
        return 'Prepared by ' . COMPANY_NAME . ' as an independent solution-fit assessment. This document '
             . 'is not endorsed by, commissioned by, or produced in collaboration with PetroSA or the '
             . 'Central Energy Fund. Plant context is drawn from publicly reported information and is '
             . 'described in general terms; no confidential operating data has been used. All numeric '
             . 'targets, tolerances and acceptance criteria in the analysis sections are ILLUSTRATIVE '
             . 'defaults chosen to show the method - they must be replaced with verified plant data, '
             . 'design basis figures and the applicable fuel specifications before this document is used '
             . 'to support any technical or commercial commitment.';
    }

    /**
     * One entry per module category:
     *   category, slug, chain (plant area), problem, platformModules[]
     *   useCases[] - each ['name','objective','perform'=>[],'analyse'=>[],'kpi']
     */
    public static function modules()
    {
        return [

            // ---------------------------------------------------------------- 1
            [
                'category' => 'Process Simulation for Energy',
                'slug'     => 'process-sim-energy',
                'chain'    => 'Feed gas reception; Gas conditioning; Syngas generation; relief and flare systems',
                'problem'  =>
                    'The front end of the refinery - acid gas removal, dehydration, guard beds and sulfur '
                  . 'recovery - was sized for the composition of the original offshore feed. As that feed '
                  . 'declines and is supplemented or replaced (higher or lower CO2, changed H2S, different '
                  . 'heavies and inerts), the treating train moves off its design point: solvent '
                  . 'circulation and reboiler duty shift, sulfur recovery efficiency changes, and the '
                  . 'relief and flare loads that were established for the original case are no longer the '
                  . 'governing ones. Without a validated model of the front end for each credible feed, '
                  . 'the plant is operated with conservative margins that cost throughput, while the '
                  . 'safety case rests on relief scenarios that may no longer bound the plant.',
                'platformModules' => [
                    'EnPharChem HYSYS', 'EnPharChem HYSYS Upstream', 'Acid Gas Cleaning',
                    'Sulsim Sulfur Recovery', 'Relief Sizing', 'EnPharChem Flare System Analyzer',
                    'BLOWDOWN Technology', 'EnPharChem HYSYS Dynamics', 'Activated Energy Analysis',
                    'EnPharChem Multi Case', 'EnPharChem Operator Training',
                ],
                'useCases' => [
                    [
                        'name'      => 'Requalify gas conditioning and sulfur recovery for a substitute feed',
                        'objective' => 'Establish whether the existing treating train can deliver syngas-grade '
                                     . 'feed on a changed gas composition, and what the binding constraint is.',
                        'perform' => [
                            'Create a project for the study and define one feed case per credible source: current offshore gas, late-life offshore gas, and regasified LNG. Enter composition, pressure, temperature and flow for each.',
                            'Build the acid gas removal train in Acid Gas Cleaning using the installed solvent, column internals and the real circulation and regeneration configuration.',
                            'Set the treated gas specification from the downstream catalyst protection requirement, not from the original design sheet - the guard beds and synthesis catalyst define the true limit.',
                            'Model the sulfur recovery unit in Sulsim against the acid gas stream produced by each case, including tail gas treatment.',
                            'Use EnPharChem Multi Case to run all feed cases as one comparable set rather than three separate studies.',
                            'For each case, record solvent circulation, reboiler and condenser duties, treated gas H2S and CO2, and sulfur recovery efficiency.',
                        ],
                        'analyse' => [
                            'Check treated gas H2S and CO2 against the catalyst protection specification for every case. A case that misses is a hard stop, not a margin discussion.',
                            'Compare required solvent circulation against installed pump and regenerator capacity. Where a case demands more than installed, the treating train - not the reformer - is the throughput limit.',
                            'Read reboiler duty as steam demand and carry it into the utilities balance; a feed change that passes on specification can still fail on energy.',
                            'Compare sulfur recovery efficiency against the site environmental permit limit for each case, including turndown.',
                            'Rank cases by binding constraint, then quantify the debottleneck cost for the preferred feed. Illustrative screening target: treated gas within specification at 100% of design gas rate with at least 10% spare solvent circulation.',
                        ],
                        'kpi' => 'Treated gas on specification for every credible feed, with the binding constraint and its debottleneck cost named per case.',
                    ],
                    [
                        'name'      => 'Revalidate relief and flare loads after a feed or configuration change',
                        'objective' => 'Confirm which relief scenario governs on the new feed basis and whether '
                                     . 'the existing flare network still handles it within its design limits.',
                        'perform' => [
                            'List the credible overpressure scenarios for the changed configuration: blocked outlet, cooling failure, power failure, fire case, and control valve failure.',
                            'Model vessel depressurisation and blowdown transients in BLOWDOWN Technology to get realistic time-dependent rates rather than steady worst-case assumptions.',
                            'Size each relief device in Relief Sizing against its governing scenario on the new feed.',
                            'Build the collection network in EnPharChem Flare System Analyzer with real header geometry, then impose the simultaneous relief loads from each scenario group.',
                            'Run the network hydraulics for tip velocity, header back-pressure and radiation at grade.',
                        ],
                        'analyse' => [
                            'Identify which scenario now governs each device. A change of governing scenario is the key finding - it invalidates the previous sizing basis even where the load is similar.',
                            'Check header back-pressure against the lowest set pressure discharging into it; back-pressure above that value means a device cannot relieve as intended.',
                            'Verify flare tip velocity stays within the burner limit to avoid flame lift-off, and check radiation contours against occupied and access areas.',
                            'Where limits are exceeded, separate genuine hardware deficiency from conservative assumption stacking by re-running with the dynamic blowdown profile.',
                            'Record every scenario, its governing status and its margin, so the relief basis is auditable against the new feed. Illustrative acceptance: all devices relieving with positive margin and radiation within the site design criterion at all access points.',
                        ],
                        'kpi' => 'A re-established, auditable relief and flare basis for the new feed, with any hardware gap quantified and scheduled.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 2
            [
                'category' => 'Process Simulation for Chemicals',
                'slug'     => 'process-sim-chemicals',
                'chain'    => 'Fischer-Tropsch synthesis; Product work-up; Water treatment',
                'problem'  =>
                    'The Fischer-Tropsch reactor and the work-up train set the product slate, and neither '
                  . 'behaves like a textbook unit. Selectivity drifts with catalyst age, temperature and '
                  . 'H2:CO ratio; the product spectrum spans light gases to wax; and the reaction water '
                  . 'carries oxygenates that complicate both separation and effluent treatment. Olefin-rich '
                  . 'and oxygenate-bearing cuts do not obey the property assumptions used for conventional '
                  . 'crude fractions, so a model built on default property methods will mispredict the '
                  . 'separation. Without a reactor model calibrated against plant yields and a rigorous '
                  . 'work-up model, the refinery cannot predict what a change in synthesis conditions does '
                  . 'to the finished product slate - which makes both catalyst management and product '
                  . 'planning reactive.',
                'platformModules' => [
                    'Flowsheet Simulator', 'EnPharChem Custom Modeler', 'EnPharChem Plus',
                    'EnPharChem Properties', 'Distillation Modeling in EnPharChem Plus',
                    'EnPharChem Adsorption', 'EnPharChem Polymers', 'Solids Modeling',
                    'Batch Modeling in EnPharChem Plus', 'Activated Economics',
                    'EnPharChem Plus Dynamics', 'EnPharChem Hybrid Models',
                ],
                'useCases' => [
                    [
                        'name'      => 'Calibrate a Fischer-Tropsch reactor model against plant yields',
                        'objective' => 'Produce a reactor model that reproduces the measured product spectrum '
                                     . 'well enough to be used for prediction, not just description.',
                        'perform' => [
                            'Assemble a clean data set: feed rate and composition, H2:CO ratio, reactor temperature and pressure, recycle rate, and the measured product distribution over a steady period. Reconcile it first - an unreconciled balance will be absorbed into the kinetic parameters as false chemistry.',
                            'Implement the synthesis kinetics and the chain-growth product distribution in EnPharChem Custom Modeler, so the model form matches the installed reactor type rather than a generic conversion block.',
                            'Define the property basis in EnPharChem Properties with explicit treatment of olefins and oxygenates; do not inherit a petroleum-fraction default.',
                            'Fit the chain-growth and selectivity parameters against the reconciled plant data set, holding physically meaningful parameters within literature bounds.',
                            'Validate against a second, independent operating period that was not used in the fit.',
                            'Where a first-principles fit will not close, use EnPharChem Hybrid Models to combine the mechanistic model with a data-driven correction, keeping the mechanistic part dominant.',
                        ],
                        'analyse' => [
                            'Compare predicted against measured yield for each product cut on the validation period, not the fitting period. Agreement on the fitting data alone proves nothing.',
                            'Check that the fitted chain-growth probability and selectivity parameters are physically plausible; a good numerical fit with implausible parameters will not extrapolate to new conditions.',
                            'Close the carbon and hydrogen balances across the reactor. An unclosed elemental balance means the product spectrum or the measured flows are wrong.',
                            'Test sensitivity to H2:CO ratio and reactor temperature and confirm the direction and magnitude of the response match plant experience.',
                            'State the validated operating envelope explicitly, and refuse to use the model outside it. Illustrative acceptance: major cuts predicted within a few percent absolute yield across the validation period, with closed elemental balances.',
                        ],
                        'kpi' => 'A reactor model validated on independent data, with a stated envelope, usable for catalyst and slate decisions.',
                    ],
                    [
                        'name'      => 'Re-rate the work-up train for a shifted product slate',
                        'objective' => 'Determine whether the separation train can deliver on-specification '
                                     . 'products when the synthesis product spectrum shifts.',
                        'perform' => [
                            'Take the reactor outlet composition from the calibrated model for both the current and the shifted selectivity case.',
                            'Build the work-up train in EnPharChem Plus with rigorous column models - real stage counts, feed locations, pumparounds and condenser configuration.',
                            'Use Distillation Modeling to set up the fractionation to the actual product cut points and the driving specifications for each product.',
                            'Model oxygenate removal from the aqueous phase, using EnPharChem Adsorption where an adsorptive step is installed, and track where oxygenates accumulate.',
                            'Where heavy wax handling matters, apply EnPharChem Polymers for the heavy end and Solids Modeling for any catalyst fines carryover.',
                            'Run both cases and record product rates, specification-driving properties, reboiler and condenser duties, and column loadings.',
                        ],
                        'analyse' => [
                            'Check each product against its specification-driving property, not against a simple boiling range. A cut on distillation but off on a key property is still off specification.',
                            'Compare column hydraulic loadings against installed capacity for the shifted case; identify the first column to flood or dry out as the true slate limit.',
                            'Track oxygenate distribution and confirm none accumulates where it is not tolerated - specifically in products or in a recycle that would concentrate it.',
                            'Carry the changed reboiler duties into the site energy balance; a favourable slate shift that raises steam demand may not be favourable overall.',
                            'Quantify quality giveaway per product and hand the figures to the blending study. Illustrative acceptance: all products on specification with no column above its hydraulic limit and giveaway reduced against the base case.',
                        ],
                        'kpi' => 'Confirmed on-specification slate for the shifted case, with the limiting column and the energy cost of the shift both named.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 3
            [
                'category' => 'Exchanger Design & Rating',
                'slug'     => 'exchanger-design',
                'chain'    => 'Syngas cooling; Fischer-Tropsch heat removal; reformer fired duty; air cooling',
                'problem'  =>
                    'Heat transfer sets the practical ceiling on a GTL plant. Syngas leaves reforming very '
                  . 'hot and its recoverable heat is a large part of the site steam balance; Fischer-Tropsch '
                  . 'synthesis is strongly exothermic and reactor temperature control depends entirely on '
                  . 'removing that heat; and a coastal site rejects final heat to air whose temperature '
                  . 'swings daily and seasonally. As surfaces foul and ambient rises, duty falls, and the '
                  . 'plant silently derates - often without anyone identifying which exchanger is '
                  . 'responsible. Fired heater efficiency compounds this: it is simultaneously a large fuel '
                  . 'consumer and a tube-integrity risk.',
                'platformModules' => [
                    'EnPharChem Shell & Tube Exchanger', 'EnPharChem Shell & Tube Mechanical',
                    'EnPharChem Air Cooled Exchanger', 'EnPharChem Fired Heater',
                    'EnPharChem Plate Exchanger', 'EnPharChem Plate Fin Exchanger',
                    'EnPharChem Coil Wound Exchanger',
                ],
                'useCases' => [
                    [
                        'name'      => 'Find the ambient-limited bottleneck in heat rejection',
                        'objective' => 'Quantify how much throughput the site loses to ambient temperature and '
                                     . 'identify which air cooler causes it.',
                        'perform' => [
                            'List the air-cooled services on the critical path and record geometry: bundle configuration, fin type and condition, fan arrangement and installed power.',
                            'Model each service in EnPharChem Air Cooled Exchanger at design geometry.',
                            'Define ambient cases from site meteorological data - winter mean, summer mean and a summer design maximum - rather than a single annual average.',
                            'Rate each exchanger at each ambient case, holding process inlet conditions fixed, and record achievable duty and process outlet temperature.',
                            'Repeat with a fouled-surface assumption representative of end-of-run condition.',
                        ],
                        'analyse' => [
                            'For each ambient case, find the service that first fails to reach its required process outlet temperature - that exchanger is the ambient bottleneck.',
                            'Convert the outlet temperature shortfall into a throughput or product-quality consequence downstream; degrees are not the deliverable, lost production is.',
                            'Separate the ambient effect from the fouling effect by comparing clean and fouled runs at the same ambient. Only one of those is fixable by cleaning.',
                            'Test whether additional fan power or a bundle change recovers the duty, and compare the parasitic power cost against the production recovered.',
                            'Feed the seasonal derate curve into planning so the production plan reflects summer reality. Illustrative target: quantified summer derate with the top two contributing services identified.',
                        ],
                        'kpi' => 'A seasonal derate curve with named limiting exchangers, separating fouling from ambient causes.',
                    ],
                    [
                        'name'      => 'Rate syngas and synthesis heat recovery for fouling and revamp',
                        'objective' => 'Establish the real end-of-run capability of critical heat recovery and '
                                     . 'test whether a revamp is justified.',
                        'perform' => [
                            'Model the syngas coolers and synthesis heat-removal exchangers in EnPharChem Shell & Tube Exchanger using as-built geometry.',
                            'Calibrate the clean model against a post-turnaround performance test, then derive the effective fouling resistance from current operating data.',
                            'Model the reformer in EnPharChem Fired Heater for efficiency, heat flux distribution and tube metal temperature.',
                            'Check mechanical acceptability of any proposed change in EnPharChem Shell & Tube Mechanical, including vibration and pressure-part adequacy.',
                            'Where a compact or cryogenic service is involved, rate it in EnPharChem Plate Fin Exchanger or EnPharChem Coil Wound Exchanger as applicable.',
                            'Build a revamp case and rate it against the same duty requirement.',
                        ],
                        'analyse' => [
                            'Compare the derived fouling resistance against the design allowance. Exceeding it explains lost steam generation and dates the next clean.',
                            'Check tube metal temperature against the alloy limit at current firing; an efficiency gain that shortens tube life is not a gain.',
                            'Trace the change in steam generation into the site balance - recovered heat displaces fired duty elsewhere, and that is where the value appears.',
                            'Confirm no vibration or mechanical exceedance in the revamp case before considering the thermal result valid.',
                            'Express the outcome as recovered duty, displaced fuel and payback against installed cost. Illustrative screening: revamp accepted only if it restores duty to within 5% of clean design without exceeding tube metal limits.',
                        ],
                        'kpi' => 'Quantified end-of-run capability, a defensible cleaning interval, and a revamp decision supported by fuel displaced.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 4
            [
                'category' => 'Concurrent FEED',
                'slug'     => 'concurrent-feed',
                'chain'    => 'Feedstock substitution projects; revamp definition; capital planning',
                'problem'  =>
                    'The decision that governs the refinery\'s future is a capital one: which feedstock '
                  . 'route to pursue, at what cost, for what availability. Options such as importing and '
                  . 'regasifying LNG, developing further offshore gas, or reconfiguring for a co-feed each '
                  . 'carry very different capital profiles, schedules and reliability consequences. When '
                  . 'these are screened in spreadsheets, estimates are built on inconsistent bases, '
                  . 'availability is assumed rather than modelled, and plot-space and routing problems '
                  . 'surface late - after the option has already been selected. The result is a business '
                  . 'case that cannot be defended and a schedule that slips for reasons that were '
                  . 'knowable at screening.',
                'platformModules' => [
                    'EnPharChem Fidelis', 'EnPharChem Capital Cost Estimator',
                    'EnPharChem In-Plant Cost Estimator', 'EnPharChem Process Economic Analyzer',
                    'EnPharChem OptiPlant 3D Layout', 'EnPharChem OptiRouter',
                    'EnPharChem Basic Engineering',
                ],
                'useCases' => [
                    [
                        'name'      => 'Screen feedstock-substitution options on a consistent capital basis',
                        'objective' => 'Rank the credible feed routes on capital and economics using one basis, '
                                     . 'early enough to influence selection.',
                        'perform' => [
                            'Define each option to a common scope boundary in EnPharChem Basic Engineering, so options are not compared at different levels of completeness.',
                            'Take the process configuration and equipment duties for each option from the front-end simulation work rather than re-estimating them.',
                            'Estimate greenfield scope in EnPharChem Capital Cost Estimator and tie-in and modification scope in EnPharChem In-Plant Cost Estimator - brownfield tie-in cost is where screening estimates usually fail.',
                            'Run economics for each option in EnPharChem Process Economic Analyzer on one price deck, one discount rate and one project life.',
                            'Check physical feasibility in EnPharChem OptiPlant 3D Layout and route the major connections in EnPharChem OptiRouter.',
                            'Record capital, operating cost, economics and any layout constraint per option in a single comparison.',
                        ],
                        'analyse' => [
                            'Compare options on both capital and unit production cost; the cheapest to build is frequently not the cheapest to run, and for an energy-intensive plant operating cost usually dominates.',
                            'Isolate the tie-in and modification portion of each estimate and scrutinise it hardest - it carries the largest uncertainty and the greatest schedule risk.',
                            'Test each option against the feed forecast from the subsurface work, including the low case. An option that only works on the optimistic forecast is a bet, not a plan.',
                            'Confirm layout feasibility before economics decide; an option that cannot be built in the available space at acceptable routing cost must be re-estimated or dropped.',
                            'Run sensitivities on gas price, product price and capital escalation, and report the break-even on each. Illustrative screening: options retained only if positive on the base deck and robust to a defined adverse gas-price case.',
                        ],
                        'kpi' => 'A ranked, single-basis option set with capital, unit cost, layout feasibility and break-even stated for each.',
                    ],
                    [
                        'name'      => 'Model availability of the selected configuration before committing',
                        'objective' => 'Convert an equipment configuration into an expected annual production '
                                     . 'figure, and find the reliability-critical items.',
                        'perform' => [
                            'Build the reliability block model of the selected configuration in EnPharChem Fidelis, following real process dependencies including utilities and feed supply.',
                            'Populate failure rates and repair times from site maintenance history where available, and from industry data elsewhere - recording which is which.',
                            'Represent sparing, intermediate storage and turndown capability explicitly; these are what convert a failure into a rate reduction rather than a shutdown.',
                            'Include feed supply interruption and grid curtailment as modelled events, not as external assumptions.',
                            'Run the simulation to a stable expected annual production and capture the contribution of each item to lost production.',
                        ],
                        'analyse' => [
                            'Compare expected annual production against the nameplate assumption in the business case. A business case built on nameplate is overstated by exactly this gap.',
                            'Rank items by contribution to lost production; the top few usually dominate and are where sparing or storage earns its cost.',
                            'Test the value of candidate mitigations - a spare machine, more intermediate storage, firmer power - by re-running and pricing the production recovered.',
                            'Separate results driven by site history from those driven by generic data, and treat conclusions resting on generic data as provisional.',
                            'Carry the availability figure into the economics rather than leaving it as a separate study. Illustrative use: economics re-run at modelled availability, with mitigations accepted only where production recovered exceeds their cost.',
                        ],
                        'kpi' => 'An expected annual production figure feeding the business case, with the reliability-critical items priced.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 5
            [
                'category' => 'Subsurface Science & Engineering',
                'slug'     => 'subsurface-science',
                'chain'    => 'Upstream gas supply that bounds everything downstream',
                'problem'  =>
                    'A GTL refinery is only as viable as its gas supply, and at Mossel Bay that supply '
                  . 'comes from mature offshore fields whose performance has declined. Every downstream '
                  . 'plan - throughput, product slate, capital programme, staffing - is conditioned on a '
                  . 'feed forecast, yet that forecast is the most uncertain number in the business. When '
                  . 'it is expressed as a single deterministic line, downstream planning inherits a false '
                  . 'confidence: the refinery commits to rates the reservoir may not deliver, or leaves '
                  . 'capacity idle against gas that was there all along. The problem is to produce a '
                  . 'probabilistic, regularly updated feed forecast that downstream planning can '
                  . 'legitimately use, and to identify the interventions that would extend feedstock life.',
                'platformModules' => [
                    'EnPharChem Subsurface Intelligence (ESI)', 'EnPharChem Tempest', 'EnPharChem Geolog',
                    'EnPharChem RMS', 'EnPharChem SKUA', 'EnPharChem GeoDepth', 'EnPharChem SeisEarth',
                    'EnPharChem EarthStudy 360', 'EnPharChem Echos', 'EnPharChem Epos',
                    'EnPharChem OpsLink',
                ],
                'useCases' => [
                    [
                        'name'      => 'Build a probabilistic feed-gas forecast for refinery planning',
                        'objective' => 'Replace a single-line gas forecast with a banded forecast carrying '
                                     . 'explicit probability, suitable for downstream commitment.',
                        'perform' => [
                            'Consolidate the subsurface data set in EnPharChem Epos and interpret well logs in EnPharChem Geolog to establish current reservoir properties.',
                            'Update the structural and property model in EnPharChem RMS or EnPharChem SKUA, honouring the latest well results rather than the original model.',
                            'History-match the reservoir in EnPharChem Tempest against measured production and pressure - a model that does not reproduce history cannot forecast.',
                            'Generate low, mid and high forecast cases by varying the parameters that history matching leaves genuinely uncertain, not by scaling the mid case.',
                            'Publish the banded forecast through EnPharChem Subsurface Intelligence with the uncertainty basis attached, and set a defined update cadence.',
                        ],
                        'analyse' => [
                            'Assess history-match quality per well and per parameter before trusting any forecast; a match achieved with unphysical parameters will diverge quickly.',
                            'Read the forecast band width as the real planning constraint. A wide band means downstream decisions must be staged or hedged, not that the mid case should be used.',
                            'Identify which uncertain parameter drives the band width - that names the measurement or well test that would most reduce planning risk.',
                            'Compare the low case against the refinery\'s minimum viable feed rate. If the low case falls below it, the refinery has a supply-security problem regardless of plant condition.',
                            'Re-forecast on the defined cadence and track forecast-versus-actual to detect drift early. Illustrative practice: quarterly update, with the low case governing any irreversible commitment.',
                        ],
                        'kpi' => 'A history-matched, banded feed forecast on a defined update cadence, with the dominant uncertainty named.',
                    ],
                    [
                        'name'      => 'Screen interventions that would extend feedstock life',
                        'objective' => 'Identify and rank subsurface interventions by gas added per unit cost '
                                     . 'and by risk.',
                        'perform' => [
                            'Reprocess or re-image the seismic over candidate areas using EnPharChem GeoDepth, EnPharChem SeisEarth or EnPharChem EarthStudy 360 to sharpen target definition.',
                            'Screen attributes in EnPharChem Echos to prioritise areas with the strongest indications.',
                            'Represent each candidate intervention - infill well, recompletion, compression - in the history-matched Tempest model.',
                            'Forecast incremental gas for each candidate against the do-nothing case, using the same uncertainty treatment as the base forecast.',
                            'Pass incremental volumes and timing to the Concurrent FEED economics so subsurface and surface options are compared on one basis.',
                            'Use EnPharChem OpsLink to keep the model updated as new well data arrives.',
                        ],
                        'analyse' => [
                            'Rank candidates by incremental gas per unit cost, but report the uncertainty band on each - a high-mean, wide-band candidate may be worse than a modest, certain one.',
                            'Check timing against the refinery\'s supply gap; gas that arrives after the plant has had to stop has far less value, and that timing sensitivity should be explicit.',
                            'Test whether candidates are additive or competing - two wells draining the same volume do not add their individual forecasts.',
                            'Assess each candidate\'s downside: what is the outcome if it underperforms, and does the plan survive that.',
                            'Compare the best subsurface option against the best surface option (such as LNG import) on delivered cost of gas to the plant gate. Illustrative basis: rank on delivered cost per unit energy at the plant gate, with the low case governing.',
                        ],
                        'kpi' => 'A ranked intervention list with banded incremental gas, timing and downside, comparable to surface feed options.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 6
            [
                'category' => 'Energy & Utilities Optimization',
                'slug'     => 'energy-optimization',
                'chain'    => 'Steam, power and fuel gas across the whole site',
                'problem'  =>
                    'Converting gas to liquids is thermodynamically expensive, and most of that cost appears '
                  . 'as fired duty, steam and power. Reforming consumes fuel, synthesis rejects large '
                  . 'quantities of heat at moderate temperature, and work-up demands reboiler duty - so the '
                  . 'site steam and power balance is not a utility afterthought but the main determinant of '
                  . 'both margin and carbon intensity. In practice these systems are operated to keep units '
                  . 'stable rather than to minimise site energy, letdown and venting absorb mismatches, and '
                  . 'recoverable heat is rejected to air. Because grid power carries both a tariff and a '
                  . 'reliability penalty, the balance also has to be optimised against when it is cheaper '
                  . 'or safer to self-generate.',
                'platformModules' => [
                    'EnPharChem Energy Analyzer', 'EnPharChem Utilities Planner',
                    'EnPharChem Strategic Planning for Sustainability Pathways',
                ],
                'useCases' => [
                    [
                        'name'      => 'Pinch-target the site and cut fired duty',
                        'objective' => 'Establish the thermodynamic minimum utility demand and the gap between '
                                     . 'it and current consumption.',
                        'perform' => [
                            'Extract stream data - flow, temperature, duty - for all significant heating and cooling services from the calibrated process models, not from design sheets.',
                            'Build composite curves in EnPharChem Energy Analyzer and establish minimum hot and cold utility targets at a defensible minimum approach temperature.',
                            'Compare current utility consumption against target to size the recoverable gap.',
                            'Generate candidate network modifications that close part of the gap, respecting plant layout and operability.',
                            'Rate each candidate match as a real exchanger so the target is not claimed on infeasible geometry.',
                            'Carry surviving candidates into the utilities balance to confirm the saving appears as reduced fired duty.',
                        ],
                        'analyse' => [
                            'Read the gap between target and actual as the maximum prize, then discount it for what is physically retrofittable - the honest number is always smaller than the thermodynamic one.',
                            'Check each proposed match for layout distance and operability; a thermodynamically ideal match across the plant is often not worth its piping and control complexity.',
                            'Confirm the saving survives exchanger rating - approach temperatures that look fine on a composite curve can demand impractical surface area.',
                            'Trace each saving to a specific fired duty or steam demand reduction; savings that cannot be traced to a fuel meter will not appear in the accounts.',
                            'Convert fuel saved into both cost and emissions. Illustrative screening: retain modifications with acceptable payback that also reduce carbon intensity, and report both figures together.',
                        ],
                        'kpi' => 'A quantified, retrofittable energy gap with each saving traced to fired duty and to emissions.',
                    ],
                    [
                        'name'      => 'Optimise the steam and power balance against tariff and curtailment',
                        'objective' => 'Decide how much to self-generate versus import, hour by hour, given '
                                     . 'tariff structure and curtailment risk.',
                        'perform' => [
                            'Model the utilities system in EnPharChem Utilities Planner: boilers, turbines, letdowns, headers and the grid connection with their real efficiency curves and limits.',
                            'Load the actual tariff structure including time-of-use periods and demand charges - an average price will produce the wrong dispatch.',
                            'Define representative operating scenarios: normal, high ambient, reduced feed rate, and grid curtailment.',
                            'Optimise generation and import for each scenario against total cost, subject to meeting all steam and power demands.',
                            'Compare the optimised dispatch against current practice and identify the specific decisions that differ.',
                            'Use EnPharChem Strategic Planning for Sustainability Pathways to test the same scenarios against emissions and decarbonisation commitments.',
                        ],
                        'analyse' => [
                            'Quantify the cost difference between optimised and current dispatch, and attribute it to specific decisions - letdown instead of turbine, import during a peak period - so operators can act on it.',
                            'Check that the optimised dispatch is feasible in every scenario including curtailment; an optimum that fails when the grid does is worthless for this site.',
                            'Identify the steam header where letdown or venting is concentrated; that is usually where the largest single recoverable loss sits.',
                            'Evaluate whether the demand-charge component justifies operating differently at peak, separately from the energy charge.',
                            'Report cost and emissions together, since they can point in opposite directions and the trade-off is a management decision, not an engineering one. Illustrative target: a dispatch rule set that is cost-optimal under normal tariff and safe under curtailment.',
                        ],
                        'kpi' => 'A scenario-tested dispatch policy with the cost and emissions consequence of each decision stated.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 7
            [
                'category' => 'Operations Support',
                'slug'     => 'operations-support',
                'chain'    => 'The live plant, and the models that are supposed to represent it',
                'problem'  =>
                    'Engineering models are built for a project and then diverge from the plant. Within a '
                  . 'year the flowsheet that justified a decision no longer matches the equipment, the '
                  . 'feed or the catalyst, and nobody can say by how much. At the same time, the '
                  . 'measurements the plant is run on are not reconciled: flows that should close a balance '
                  . 'do not, and the discrepancy is absorbed silently into whichever number is trusted '
                  . 'least. The result is a plant operated on a mixture of stale models and unreconciled '
                  . 'instruments - so when performance drops, there is no reliable reference to compare '
                  . 'against, and disagreement about the data displaces analysis of the problem.',
                'platformModules' => [
                    'EnPharChem OnLine', 'EnPharChem Simulation Workbook',
                ],
                'useCases' => [
                    [
                        'name'      => 'Deploy an online reconciled model of the syngas and synthesis loop',
                        'objective' => 'Maintain a live, data-reconciled model that tracks the plant and '
                                     . 'exposes measurement error.',
                        'perform' => [
                            'Take the validated offline model of the loop as the starting point; an unvalidated model online just produces wrong answers faster.',
                            'Connect EnPharChem OnLine to the historian tags for the loop, with explicit measurement uncertainty on each tag.',
                            'Configure data reconciliation over the redundant measurement set so the model closes balances by adjusting measurements within their stated uncertainty.',
                            'Enable gross error detection to flag instruments whose readings cannot be reconciled at all.',
                            'Define the performance parameters to be tracked - conversion, selectivity, exchanger fouling factors, efficiency - and record them every cycle.',
                            'Run in shadow mode against operator judgement before anyone relies on the output.',
                        ],
                        'analyse' => [
                            'Review the size and direction of reconciliation adjustments per instrument; a tag consistently adjusted in one direction is miscalibrated, not noisy.',
                            'Treat gross error flags as instrument work orders, not as model failures - this is the most immediate value the system delivers.',
                            'Trend reconciled performance parameters rather than raw readings; fouling and catalyst decay are visible in the trend long before they are visible in a single reading.',
                            'Compare reconciled results against laboratory analyses at each sample point to confirm the model is tracking reality and not just closing arithmetically.',
                            'Set alert thresholds on parameter drift rather than on absolute values, so gradual degradation is caught. Illustrative acceptance: balances closing within measurement uncertainty and every gross error flag resolved to an instrument action.',
                        ],
                        'kpi' => 'A live reconciled model closing balances within uncertainty, with instrument errors surfaced as actions.',
                    ],
                    [
                        'name'      => 'Publish engineering checks as a shared Simulation Workbook',
                        'objective' => 'Put routine calculations into the hands of shift and process engineers '
                                     . 'without requiring them to run the full model.',
                        'perform' => [
                            'Identify the recurring calculations engineers actually repeat - compressor performance checks, exchanger duty verification, product property estimates.',
                            'Expose each as a controlled worksheet in EnPharChem Simulation Workbook, backed by the validated model rather than by a private spreadsheet.',
                            'Fix the input ranges and units so out-of-envelope entries are rejected instead of silently producing nonsense.',
                            'Version the workbooks and record which model revision each is bound to.',
                            'Train the users, then retire the private spreadsheets the workbooks replace - otherwise both persist and disagree.',
                        ],
                        'analyse' => [
                            'Check workbook output against the full model for a set of known cases before release; a convenient wrong answer is worse than an inconvenient right one.',
                            'Monitor which workbooks are actually used - unused ones indicate the real question was different from the one that was automated.',
                            'Review rejected out-of-envelope entries; a pattern of them means the plant is operating outside the model\'s validated range and the model needs extending.',
                            'Confirm each workbook is re-validated whenever its underlying model revision changes, and treat an unbound workbook as unusable.',
                            'Track the reduction in ad-hoc calculation requests as the practical measure of success. Illustrative target: the routine checks answered from controlled workbooks rather than private files.',
                        ],
                        'kpi' => 'Routine engineering checks answered from validated, versioned workbooks instead of private spreadsheets.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 8
            [
                'category' => 'Advanced Process Control',
                'slug'     => 'advanced-process-control',
                'chain'    => 'Reforming, Fischer-Tropsch synthesis and product fractionation',
                'problem'  =>
                    'The units that matter most at this plant are run with margin held back for safety. '
                  . 'Reformer firing, synthesis reactor temperature and column operation are all kept away '
                  . 'from their true constraints because the consequence of crossing one is severe and the '
                  . 'feedback telling an operator how close they are is slow. Product quality is confirmed '
                  . 'by laboratory analysis with hours of dead time, so columns are over-refluxed to '
                  . 'guarantee specification, spending energy to buy certainty. Feed rate and composition '
                  . 'transitions - unavoidable with a declining or substituted gas supply - are handled '
                  . 'manually, and every transition costs off-specification product and time.',
                'platformModules' => [
                    'EnPharChem DMC3', 'EnPharChem DMC3 Builder', 'EnPharChem Virtual Advisor (EVA) for DMC3',
                    'EnPharChem Inferential Qualities', 'EnPharChem Nonlinear Controller',
                    'EnPharChem Transition Management', 'EnPharChem Watch Performance Monitor',
                ],
                'useCases' => [
                    [
                        'name'      => 'Push the synthesis loop to its real constraint with multivariable control',
                        'objective' => 'Recover the margin currently held back manually, without moving the '
                                     . 'true constraint.',
                        'perform' => [
                            'Establish the constraint set explicitly: reactor temperature limits, metallurgical limits, compressor capacity, downstream hydraulic limits. This is a safety review, not a control exercise.',
                            'Run plant step tests and identify the multivariable model in EnPharChem DMC3 Builder, ensuring the tests cover the interactions and not just single moves.',
                            'Configure the controller in EnPharChem DMC3 with constraints as hard limits and economics as the objective, so it drives to the constraint rather than to a set point.',
                            'Where the response is strongly nonlinear across the operating range, use EnPharChem Nonlinear Controller instead of stretching a linear model.',
                            'Commission in prediction-only mode first, comparing predictions against actual before closing the loop.',
                            'Deploy EnPharChem Watch Performance Monitor and EnPharChem Virtual Advisor (EVA) for DMC3 to track sustained performance.',
                        ],
                        'analyse' => [
                            'Compare model prediction against actual response over commissioning; a controller with poor prediction will be turned off by operators within weeks, whatever the benefit study said.',
                            'Measure the reduction in standard deviation of the constrained variables - that reduction is what allows the mean to move closer to the limit, and it is the real mechanism of benefit.',
                            'Quantify benefit as the movement of the operating mean toward the constraint, converted to throughput or yield. Do not claim benefit from variance reduction alone.',
                            'Track controller service factor and the reasons for every operator intervention; a controller in service 60% of the time delivers 60% of nothing if the drop-outs cluster at high rates.',
                            'Confirm no constraint was moved or relaxed to obtain the benefit; if one was, that is a safety change requiring separate approval. Illustrative acceptance: high service factor with the operating mean measurably closer to the unchanged constraint.',
                        ],
                        'kpi' => 'Sustained high controller service factor with the operating mean moved toward unchanged constraints.',
                    ],
                    [
                        'name'      => 'Replace laboratory lag with inferential quality models',
                        'objective' => 'Give the control system a continuous quality estimate so columns need '
                                     . 'less giveaway to stay on specification.',
                        'perform' => [
                            'Select the qualities that actually drive giveaway - the specification each column is over-refluxing to protect.',
                            'Assemble paired process and laboratory data over a period covering the full range of grades and feed conditions, with correct time alignment between sample and process conditions.',
                            'Build the inferential models in EnPharChem Inferential Qualities, preferring a physically sensible input set over the best-correlating one.',
                            'Validate against laboratory results held out of the model build.',
                            'Commission the inferentials as controlled variables in DMC3, with laboratory feedback for bias update rather than as a hard override.',
                            'Add EnPharChem Transition Management to handle grade and feed changes without manual intervention.',
                        ],
                        'analyse' => [
                            'Assess prediction error against held-out laboratory data across the whole operating range, paying particular attention to the extremes where giveaway decisions are actually made.',
                            'Check that model inputs are physically defensible; a model leaning on a spurious correlation will fail exactly when conditions change.',
                            'Verify the bias-update mechanism handles laboratory outliers without chasing them, and that a bad sample cannot drive the plant off specification.',
                            'Measure the reduction in quality giveaway and the associated reboiler duty saved - this is where the inferential pays for itself.',
                            'Confirm off-specification frequency does not rise as giveaway falls; that trade-off is the whole risk of the exercise. Illustrative acceptance: measurable giveaway reduction with off-specification incidents no higher than the baseline.',
                        ],
                        'kpi' => 'Reduced quality giveaway and reboiler duty at unchanged or better specification compliance.',
                    ],
                ],
            ],

            // ---------------------------------------------------------------- 9
            [
                'category' => 'Dynamic Optimization',
                'slug'     => 'dynamic-optimization',
                'chain'    => 'The full conversion chain, optimised as one system',
                'problem'  =>
                    'Each area of the refinery can be optimised and the site as a whole still leave money '
                  . 'on the table, because the areas are strongly coupled. The H2:CO ratio leaving reforming '
                  . 'sets what the synthesis reactor can do; synthesis selectivity sets what the work-up '
                  . 'train must separate; the work-up train\'s reboiler demand competes with reforming for '
                  . 'fuel and steam; and recycle ties the whole chain back on itself. Optimising these '
                  . 'locally, on schedules set by different departments, produces a sequence of locally '
                  . 'sensible decisions that are jointly suboptimal - and the imbalance shifts whenever '
                  . 'feed or product prices move, which under current conditions is constantly.',
                'platformModules' => [
                    'EnPharChem GDOT',
                ],
                'useCases' => [
                    [
                        'name'      => 'Plant-wide real-time optimisation across the conversion chain',
                        'objective' => 'Coordinate set points across reforming, synthesis and work-up against '
                                     . 'one site objective.',
                        'perform' => [
                            'Define the site objective function explicitly in economic terms, including feed cost, product values, fuel and power - and get it agreed before configuring anything, because it encodes a commercial policy.',
                            'Build the plant-wide model in EnPharChem GDOT spanning reforming, synthesis and work-up, with the recycle structure represented properly.',
                            'Reconcile against live plant data so the optimiser starts from the actual operating point rather than a nominal one.',
                            'Set the handover to the underlying advanced controllers, so GDOT moves controller targets and the controllers keep constraints.',
                            'Run in advisory mode, logging recommendations and the outcome when operators accept or reject them.',
                            'Close the loop only on the recommendation classes that proved reliable in advisory mode.',
                        ],
                        'analyse' => [
                            'Compare recommended against actual operation and quantify the gap in objective terms; that gap is the size of the coordination problem.',
                            'Review rejected recommendations with the operators. A recommendation rejected for a good reason means the model is missing a real constraint - that is a finding, not resistance.',
                            'Confirm recommendations respect every constraint held by the advanced controllers; an optimiser that asks for a constraint violation will be switched off permanently.',
                            'Check stability of the recommendations. An optimiser that oscillates between operating points as prices move is unusable regardless of its theoretical benefit.',
                            'Attribute benefit to specific coordination decisions rather than reporting a single site number, so the claim can be audited. Illustrative acceptance: stable recommendations, no constraint violations, and benefit traceable to named decisions.',
                        ],
                        'kpi' => 'Stable, constraint-respecting coordination with benefit attributed to specific decisions.',
                    ],
                    [
                        'name'      => 'Re-optimise the product slate to the current price deck',
                        'objective' => 'Move the slate as relative product values change, within what the plant '
                                     . 'can actually make.',
                        'perform' => [
                            'Load the current price deck and update it on a defined cadence - a stale deck optimises for last quarter\'s market.',
                            'Constrain the optimisation with the validated synthesis and work-up envelope, so it cannot recommend a slate the plant cannot produce.',
                            'Include specification limits and tankage constraints as hard limits, not as penalties to be traded away.',
                            'Run the optimisation for the current deck and for a set of plausible alternative decks.',
                            'Compare the recommended slate against the current plan and reconcile the difference with the planning team.',
                        ],
                        'analyse' => [
                            'Identify which product value drives the recommended shift, and how far it must move before the recommendation reverses. That switching point is more useful operationally than the recommendation itself.',
                            'Verify the recommended slate is achievable within the validated envelope, and treat any recommendation outside it as a modelling artefact.',
                            'Check the energy consequence of the shift; a higher-value slate that needs more reboiler duty may be worth less than it appears.',
                            'Confirm tankage and dispatch can absorb the changed slate before treating the benefit as real.',
                            'Reconcile against the supply-chain plan so the plant is not optimising against a different price basis from the planners. Illustrative practice: monthly re-optimisation with switching points published to planning and operations.',
                        ],
                        'kpi' => 'A price-responsive slate recommendation with switching points and energy consequences stated.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 10
            [
                'category' => 'Manufacturing Execution Systems',
                'slug'     => 'mes',
                'chain'    => 'Production accounting, movements, tankage and the production record',
                'problem'  =>
                    'The refinery cannot reliably say what it made, from what, and where it went. Production '
                  . 'accounting depends on unreconciled meters and spreadsheets assembled after the fact, so '
                  . 'daily and monthly balances disagree and the difference is written off as unaccounted '
                  . 'loss. Movements between units and tanks are recorded inconsistently, which makes '
                  . 'inventory a matter of reconciliation rather than record. Because there is no single '
                  . 'auditable production record, questions about yield or loss become archaeology, and by '
                  . 'the time a genuine loss is confirmed the operating conditions that caused it have '
                  . 'changed. For a plant whose feed is scarce and expensive, an unexplained loss is '
                  . 'directly a margin loss.',
                'platformModules' => [
                    'EnPharChem InfoPlus.21', 'enPharChemONE Process Explorer',
                    'EnPharChem Production Record Manager', 'EnPharChem Production Execution Manager',
                    'EnPharChem Unified Reconciliation and Accounting',
                    'EnPharChem Operations Reconciliation and Accounting', 'EnPharChem Unified Movements',
                    'EnPharChem Tank and Operations Manager',
                ],
                'useCases' => [
                    [
                        'name'      => 'Close a daily mass balance and locate unaccounted loss',
                        'objective' => 'Produce a reconciled daily balance whose closure error is small enough '
                                     . 'to make a real loss visible.',
                        'perform' => [
                            'Establish the historian foundation in EnPharChem InfoPlus.21 so every meter, tank level and analyser used in the balance comes from one time-aligned source.',
                            'Define the balance boundary and every stream crossing it - including fuel gas, flare, vents and reaction water, which are where unaccounted loss usually hides.',
                            'Configure reconciliation in EnPharChem Unified Reconciliation and Accounting with realistic measurement uncertainties, and let it distribute closure error accordingly.',
                            'Record movements through EnPharChem Unified Movements and tank inventory through EnPharChem Tank and Operations Manager, so inventory change is a record rather than a back-calculation.',
                            'Run the balance daily and publish closure error and the reconciled adjustment per stream.',
                            'Investigate persistent adjustments using enPharChemONE Process Explorer to correlate them with operating conditions.',
                        ],
                        'analyse' => [
                            'Track closure error over time. A stable small error means the balance is trustworthy; a drifting error means an instrument or a real loss is developing.',
                            'Examine reconciled adjustments per stream: a stream repeatedly adjusted in the same direction is a measurement problem, while one adjusted erratically is usually a genuine process variation.',
                            'Separate measurement error from real loss by checking whether the adjustment correlates with an operating condition. Correlation points to process; no correlation points to instrumentation.',
                            'Quantify unaccounted loss in feed-cost terms - that is what makes the case for fixing it, and for a scarce feed it is a large number.',
                            'Confirm daily balances aggregate to the monthly balance; a discrepancy between them means the aggregation, not the plant, is at fault. Illustrative target: closure error within measurement uncertainty and every persistent adjustment assigned a cause.',
                        ],
                        'kpi' => 'A daily balance closing within uncertainty, with unaccounted loss quantified in feed cost and assigned a cause.',
                    ],
                    [
                        'name'      => 'Establish an auditable electronic production record',
                        'objective' => 'Replace reconstructed paperwork with a contemporaneous, auditable record '
                                     . 'of what was produced and under what conditions.',
                        'perform' => [
                            'Define the production record content per product: quantities, qualities, the operating conditions that produced them, and the deviations raised.',
                            'Configure EnPharChem Production Record Manager to assemble the record automatically from historian and laboratory data as production happens.',
                            'Use EnPharChem Production Execution Manager to drive and record the operating steps, so execution and record are the same act rather than two.',
                            'Set the review and approval workflow with defined roles, and make approval a real gate rather than a formality.',
                            'Configure exception handling so deviations are recorded, explained and closed rather than silently normalised.',
                            'Retain records to the applicable retention requirement with access control.',
                        ],
                        'analyse' => [
                            'Audit a sample of records end to end for completeness and traceability - can every quality figure be traced to a sample and every quantity to a meter or tank movement.',
                            'Review deviation frequency by type; clustering points to a systemic process or procedural problem rather than isolated error.',
                            'Check that approval is genuinely exercised: approvals granted without recorded review indicate the workflow is theatre and the record\'s value is illusory.',
                            'Measure the time from production to complete record. A long lag means the record is still being reconstructed rather than captured.',
                            'Confirm the record supports the questions it exists to answer - yield queries, quality disputes, loss investigations - by testing it against real past questions. Illustrative target: records complete within one day of production and traceable on audit.',
                        ],
                        'kpi' => 'Contemporaneous, traceable production records that answer yield and quality questions without reconstruction.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 11
            [
                'category' => 'Petroleum Supply Chain',
                'slug'     => 'petroleum-supply-chain',
                'chain'    => 'Planning, blending and scheduling of the finished product slate',
                'problem'  =>
                    'Planning a GTL refinery is not planning a crude refinery. The feed is gas, its '
                  . 'availability is uncertain, and the synthetic product streams have properties unlike '
                  . 'the crude fractions that conventional planning tools assume - highly paraffinic, '
                  . 'essentially sulfur-free, and in some cases olefinic or oxygenate-bearing. Those '
                  . 'properties are commercially valuable but only if blending exploits them while still '
                  . 'meeting national fuel specifications on every property. Planning done on spreadsheets '
                  . 'and rules of thumb systematically gives quality away, and schedules built '
                  . 'independently of the gas forecast and tankage constraints have to be reworked as soon '
                  . 'as reality intervenes.',
                'platformModules' => [
                    'EnPharChem Unified PIMS', 'EnPharChem PIMS-AO',
                    'EnPharChem Virtual Advisor (EVA) for Unified PIMS', 'EnPharChem Verify for Planning',
                    'EnPharChem Assay Management', 'EnPharChem Petroleum Scheduler',
                    'EnPharChem Refinery Multi-Blend Optimizer', 'EnPharChem Unified Scheduling',
                    'EnPharChem Unified Multisite', 'EnPharChem Petroleum Supply Chain Planner',
                    'EnPharChem Collaborative Demand Manager',
                ],
                'useCases' => [
                    [
                        'name'      => 'Build a planning model for the GTL slate against national fuel specifications',
                        'objective' => 'Create a planning basis that reflects what this plant can actually make '
                                     . 'and sell, on the real gas forecast.',
                        'perform' => [
                            'Characterise the synthetic streams in EnPharChem Assay Management with the properties that actually drive blending decisions, rather than mapping them onto conventional crude fractions.',
                            'Build the refinery planning model in EnPharChem Unified PIMS with process yields taken from the calibrated simulation models, so the plan is bounded by real plant capability.',
                            'Encode the applicable national fuel specifications as hard constraints on every regulated property.',
                            'Drive the model from the banded gas forecast produced by the subsurface work - plan the low case as well as the mid case.',
                            'Apply EnPharChem PIMS-AO where nonlinear process behaviour materially affects the optimum, and validate the plan in EnPharChem Verify for Planning.',
                            'Reconcile the plan against demand from EnPharChem Collaborative Demand Manager.',
                        ],
                        'analyse' => [
                            'Check which constraints are binding in the optimal plan; the binding set tells you where the refinery actually loses value, and it is often not where attention is focused.',
                            'Compare plans on the low, mid and high gas cases and identify the decisions that change between them. Those are the decisions that must be staged rather than committed.',
                            'Validate planning yields against the simulation models and the reconciled production data; a plan built on optimistic yields will be missed every month.',
                            'Examine shadow prices on the specification constraints to see which property limits are most costly - that names the blending or process change worth pursuing.',
                            'Confirm the plan is executable against tankage and dispatch before publishing it. Illustrative acceptance: plans for all three gas cases with binding constraints and staged decisions identified.',
                        ],
                        'kpi' => 'A validated plan for each gas case, with binding constraints, shadow prices and staged decisions named.',
                    ],
                    [
                        'name'      => 'Optimise blending to specification with minimum giveaway',
                        'objective' => 'Blend to specification rather than comfortably inside it, and schedule '
                                     . 'the blends against real tankage.',
                        'perform' => [
                            'Set up blend recipes in EnPharChem Refinery Multi-Blend Optimizer with component availabilities, qualities and the full specification set per product.',
                            'Include all regulated properties as constraints, and use measured component qualities from the laboratory rather than assumed values.',
                            'Optimise across simultaneous blends rather than one at a time, since components compete between blends.',
                            'Schedule the optimised blends in EnPharChem Petroleum Scheduler against tank availability, dispatch windows and changeover constraints.',
                            'Use EnPharChem Unified Scheduling to align the blend schedule with production and movements, and EnPharChem Unified Multisite if coastal distribution points are in scope.',
                        ],
                        'analyse' => [
                            'Measure giveaway per property per blend against the specification limit. Giveaway is the direct measure of value lost, and it is usually concentrated in one or two properties.',
                            'Identify which component is scarce across simultaneous blends - that scarcity, not the individual recipe, is what drives total giveaway.',
                            'Check the blend schedule is feasible against tankage and changeovers; an optimal recipe that cannot be tanked is not a plan.',
                            'Quantify the risk of reblending: how much specification margin is needed to avoid it, given measurement uncertainty on component qualities. Cutting margin below that point costs more than it saves.',
                            'Track realised versus planned blend quality to confirm the optimiser is trustworthy in practice. Illustrative acceptance: measurable giveaway reduction with no increase in reblends.',
                        ],
                        'kpi' => 'Reduced quality giveaway on a tankage-feasible blend schedule, with no rise in reblending.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 12
            [
                'category' => 'Supply Chain Management',
                'slug'     => 'supply-chain-mgmt',
                'chain'    => 'Demand planning, distribution and inventory to the market',
                'problem'  =>
                    'The refinery sits at the end of a long and constrained logistics chain, supplying '
                  . 'markets whose demand varies while its own production is limited by feed availability. '
                  . 'When demand planning, production planning and distribution are run separately, the '
                  . 'plant is asked for volumes it cannot make, inventory is held in the wrong place, and '
                  . 'stock-outs coexist with excess cover. The scarcity of feed sharpens this: with limited '
                  . 'production, allocating it to the highest-value demand matters far more than it would '
                  . 'at a plant that could simply make more. Without a single planning process linking '
                  . 'demand, constrained production and distribution, that allocation is made implicitly '
                  . 'and badly.',
                'platformModules' => [
                    'enPharChemONE Supply Chain Management', 'EnPharChem Supply Chain Planner',
                    'EnPharChem Plant Scheduler Family', 'EnPharChem Scheduler Explorer',
                    'EnPharChem Collaborative Demand Manager', 'EnPharChem Supply Chain Management Insights',
                ],
                'useCases' => [
                    [
                        'name'      => 'Run sales and operations planning against constrained production',
                        'objective' => 'Reach one agreed plan that respects both what the market wants and what '
                                     . 'the plant can make.',
                        'perform' => [
                            'Build the demand forecast in EnPharChem Collaborative Demand Manager with the commercial team, capturing seasonality and known customer commitments.',
                            'Take the production envelope from the refinery planning model on each gas case, rather than from an aspirational nameplate figure.',
                            'Balance demand against the constrained envelope in EnPharChem Supply Chain Planner, and where demand exceeds supply make the allocation explicit and priced.',
                            'Run the planning cycle on a fixed cadence with the same participants, so the plan is a decision rather than a document.',
                            'Publish the agreed plan to production scheduling and distribution, and track adherence in EnPharChem Supply Chain Management Insights.',
                        ],
                        'analyse' => [
                            'Compare demand against the constrained production envelope and quantify the shortfall per product. That shortfall is the allocation decision - naming it is the point of the exercise.',
                            'Evaluate allocation against margin and contractual obligation together; the highest-margin customer is not automatically the right one to serve if a commitment exists.',
                            'Track forecast accuracy by product and by period, and hold the forecast owner to it. An unmeasured forecast will not improve.',
                            'Measure plan adherence on both sides - production against plan and sales against forecast - since blame usually lands on whichever is measured.',
                            'Test the plan against the low gas case and record what would be cut first. Illustrative practice: monthly cycle with allocation priced, forecast accuracy tracked, and a rehearsed low-case cut sequence.',
                        ],
                        'kpi' => 'One agreed plan per cycle with priced allocation, measured forecast accuracy and a rehearsed low-case response.',
                    ],
                    [
                        'name'      => 'Optimise distribution and inventory cover',
                        'objective' => 'Hold the least inventory consistent with the service level, in the right '
                                     . 'locations.',
                        'perform' => [
                            'Map the distribution network - depots, transport modes, lead times and capacities - into EnPharChem Supply Chain Planner.',
                            'Set the target service level per product and location as a commercial decision, made explicitly rather than inherited.',
                            'Calculate required safety stock from demand variability and lead-time variability, not from a uniform days-of-cover rule.',
                            'Optimise replenishment and mode selection against total landed cost, including the cost of the inventory itself.',
                            'Schedule plant output against the resulting lifting pattern using EnPharChem Plant Scheduler Family, and review with EnPharChem Scheduler Explorer.',
                        ],
                        'analyse' => [
                            'Compare current against calculated safety stock per location; uniform days-of-cover rules almost always over-stock stable products and under-stock volatile ones.',
                            'Check achieved service level against target rather than against average performance - service failures cluster, and averages hide them.',
                            'Analyse total landed cost by mode, including the inventory carrying cost that a slower, cheaper mode imposes.',
                            'Test the network against disruption - a transport interruption or an unplanned plant outage - and identify which location fails first.',
                            'Confirm the lifting pattern is feasible for plant scheduling and tankage before adopting it. Illustrative target: service level met at lower total inventory, with the first-failure location known.',
                        ],
                        'kpi' => 'Target service level achieved at lower total landed cost, with disruption exposure identified.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 13
            [
                'category' => 'Asset Performance Management',
                'slug'     => 'apm',
                'chain'    => 'Rotating and fired equipment; catalyst and reactor condition',
                'problem'  =>
                    'The plant\'s critical machines - syngas and recycle compressors, blowers, large pumps '
                  . 'and fired equipment - are mature, and some have spent periods at reduced rate or idle, '
                  . 'which brings its own damage mechanisms. An unplanned failure on any of them stops the '
                  . 'conversion chain, and spares for this class of machine have long lead times, so a '
                  . 'failure is measured in weeks of lost production rather than days. Meanwhile catalyst '
                  . 'and reactor performance degrade gradually, and by the time the degradation is obvious '
                  . 'in a single measurement, the opportunity to intervene cheaply has passed. Condition '
                  . 'monitoring based on fixed alarm limits detects problems that are already severe; what '
                  . 'is missing is early detection of the pattern that precedes failure.',
                'platformModules' => [
                    'EnPharChem Mtell', 'EnPharChem ProMV', 'EnPharChem Process Pulse',
                    'EnPharChem Unscrambler',
                ],
                'useCases' => [
                    [
                        'name'      => 'Deploy failure-pattern monitoring on the critical machines',
                        'objective' => 'Detect the developing signature of a known failure mode early enough '
                                     . 'to plan the intervention.',
                        'perform' => [
                            'Rank machines by production consequence of failure combined with spare lead time, and start with the top few rather than instrumenting everything.',
                            'Assemble historical operating data covering both normal running and past failures - the failures are what make supervised detection possible.',
                            'Build failure agents in EnPharChem Mtell for the specific known failure modes of each machine, rather than a generic anomaly detector.',
                            'Establish the normal-behaviour baseline across the full range of operating conditions, so a rate change is not read as a fault.',
                            'Set alert routing into the maintenance work process, with an owner and a defined response for each alert type.',
                            'Add EnPharChem Process Pulse for continuous monitoring of the surrounding process conditions.',
                        ],
                        'analyse' => [
                            'Measure lead time between first alert and the failure it predicted; lead time shorter than the spare procurement time provides warning but no useful option.',
                            'Track false positive rate honestly. Alerts that prove groundless will be ignored within weeks, and the system\'s credibility is spent once.',
                            'Confirm each alert maps to a physically sensible degradation mechanism, so maintenance knows what to inspect rather than just that something is wrong.',
                            'Check the baseline holds across rate and ambient changes, since this plant runs at variable rate and a rate-sensitive baseline will alarm on normal operation.',
                            'Quantify avoided downtime per confirmed catch, in production terms, to sustain the programme. Illustrative target: useful lead time on the critical machines with a false positive rate low enough that alerts are still trusted.',
                        ],
                        'kpi' => 'Actionable lead time on critical machines with a false positive rate low enough to retain trust.',
                    ],
                    [
                        'name'      => 'Multivariate monitoring of reactor and catalyst condition',
                        'objective' => 'See catalyst and reactor degradation in the multivariable pattern before '
                                     . 'it is visible in any single measurement.',
                        'perform' => [
                            'Select the variable set that jointly describes reactor condition: temperatures and profile, pressure drop, feed composition, H2:CO ratio, conversion and selectivity.',
                            'Build the multivariate model in EnPharChem ProMV on a period of known-good operation covering the normal range.',
                            'Use EnPharChem Unscrambler for exploratory analysis where the driving variables are not yet known.',
                            'Monitor multivariate distance from the good-operation model, and inspect contributions when it rises.',
                            'Correlate excursions against catalyst age, feed changes and any upset history to interpret them.',
                            'Set thresholds on the rate of drift as well as on absolute deviation.',
                        ],
                        'analyse' => [
                            'Read variable contributions when the multivariate statistic rises - the contribution pattern, not the statistic, tells you which mechanism is developing.',
                            'Distinguish catalyst deactivation from operating-condition change by testing whether the deviation persists after conditions return to baseline.',
                            'Compare deactivation rate against the expected curve for the catalyst; faster than expected points to poisoning or maldistribution rather than normal ageing.',
                            'Use the trend to forecast when performance reaches the intervention threshold, so a change-out can be planned into a turnaround rather than forced between them.',
                            'Validate conclusions against direct evidence - catalyst samples, inspection findings - before acting on them. Illustrative target: degradation detected early enough to schedule intervention into planned downtime.',
                        ],
                        'kpi' => 'Early, mechanism-attributed detection of degradation, with intervention scheduled into planned downtime.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 14
            [
                'category' => 'Industrial Data Fabric',
                'slug'     => 'industrial-data-fabric',
                'chain'    => 'Every layer, from offshore telemetry to the finished product record',
                'problem'  =>
                    'Every improvement described in this document depends on data that currently lives in '
                  . 'incompatible silos: offshore telemetry, refinery control systems, the historian, '
                  . 'laboratory systems, maintenance records and business systems. Each has its own naming, '
                  . 'timestamps and access path, and none shares a common notion of what a unit or a stream '
                  . 'is. The practical consequence is that every analysis begins with weeks of data '
                  . 'plumbing, different teams answer the same question differently because they assembled '
                  . 'different data sets, and advanced applications - control, optimisation, asset '
                  . 'monitoring - each maintain their own fragile integration. Until the data layer is '
                  . 'unified and contextualised, every other initiative pays this tax repeatedly.',
                'platformModules' => [
                    'EnPharChem Inmation',
                ],
                'useCases' => [
                    [
                        'name'      => 'Unify operational data into one contextualised model',
                        'objective' => 'Create a single, governed access layer where data is identified by what '
                                     . 'it is, not by where it happens to be stored.',
                        'perform' => [
                            'Inventory the sources - control systems, historian, laboratory, maintenance, business systems - and record for each what it is authoritative for. Two sources claiming the same fact is the core problem to resolve.',
                            'Connect the sources through EnPharChem Inmation without copying everything into another silo.',
                            'Build the asset and process context model so tags are addressable by unit, stream and equipment rather than by controller tag name.',
                            'Define the single authoritative source per data element and enforce it, so conflicting answers become impossible rather than merely discouraged.',
                            'Normalise time bases and units at the fabric layer, since misaligned timestamps silently corrupt every downstream calculation.',
                            'Apply access control and audit consistent with the platform\'s role model.',
                        ],
                        'analyse' => [
                            'Test the context model by asking real engineering questions through it; if answering still requires knowing tag names, the contextualisation has not been done.',
                            'Verify time alignment explicitly across sources - misalignment between a laboratory sample and process conditions is the most common cause of a wrong model.',
                            'Check that unit and scaling conversions are correct at the boundary, comparing a known value through the fabric against its source.',
                            'Confirm no data element has two authoritative sources; where one does, resolve it rather than documenting the ambiguity.',
                            'Measure how long a typical analysis takes to assemble its data before and after. Illustrative target: routine analyses sourcing data through the fabric without bespoke extraction.',
                        ],
                        'kpi' => 'One contextualised, time-aligned, access-controlled data layer with a single authoritative source per element.',
                    ],
                    [
                        'name'      => 'Serve a governed data layer to the advanced applications',
                        'objective' => 'Have control, optimisation, MES and asset monitoring consume the same '
                                     . 'governed data instead of private integrations.',
                        'perform' => [
                            'List the consuming applications and the data each needs, including update frequency and latency tolerance.',
                            'Publish governed interfaces from the fabric for each consumer rather than letting each build its own extraction.',
                            'Verify latency and refresh rate meet the needs of the most demanding consumer - real-time optimisation and control are unforgiving here.',
                            'Migrate consumers one at a time, running old and new in parallel until results agree.',
                            'Retire the private integrations after migration; leaving them in place recreates the silos.',
                            'Monitor fabric availability, since it is now a dependency for everything that consumes it.',
                        ],
                        'analyse' => [
                            'Compare application results before and after migration on identical periods; any difference must be explained before the old path is retired, because it means one of them was wrong.',
                            'Measure delivered latency against each consumer\'s requirement, not against an average.',
                            'Assess the availability consequence of centralisation - the fabric is now a single point of failure and must be engineered accordingly.',
                            'Confirm no consumer has quietly retained a private integration, since a hidden path will drift and eventually contradict the governed one.',
                            'Track reduction in integration maintenance effort as the practical measure of benefit. Illustrative acceptance: all consumers on governed interfaces with results reconciled and private paths retired.',
                        ],
                        'kpi' => 'All advanced applications consuming governed interfaces, with reconciled results and no private integrations left.',
                    ],
                ],
            ],

            // --------------------------------------------------------------- 15
            [
                'category' => 'Digital Grid Management',
                'slug'     => 'digital-grid-mgmt',
                'chain'    => 'Site electrical supply, generation and resilience',
                'problem'  =>
                    'A continuous conversion plant cannot absorb an unplanned power interruption cheaply: '
                  . 'losing power means losing the synthesis loop, and recovery takes far longer than the '
                  . 'outage. Operating in a system subject to national load curtailment therefore makes '
                  . 'electrical resilience a production problem, not a facilities one. The site has '
                  . 'on-site generation, but if that generation, the connection to the grid and the '
                  . 'internal distribution network are managed as separate concerns, the plant cannot plan '
                  . 'a controlled response to curtailment, cannot island reliably, and cannot decide '
                  . 'economically when to self-generate. Every unplanned electrical event then becomes a '
                  . 'production event.',
                'platformModules' => [
                    'EnPharChem Microgrid Management System', 'EnPharChem OSI Monarch SCADA',
                    'EnPharChem OSI Generation Management System', 'EnPharChem OSI Energy Management System',
                    'EnPharChem OSI Advanced Distribution Management System',
                    'EnPharChem OSI Distributed Energy Resource Management System',
                    'EnPharChem OSI CHRONUS Historian', 'EnPharChem OSI Continua Pipeline Management',
                    'EnPharChem Cimphony Network Model Management', 'EnPharChem Grid Apps',
                    'EnPharChem Grid Reporter', 'EnPharChem DER Connect', 'EnPharChem Network Maps',
                    'EnPharChem Resilience Portal',
                ],
                'useCases' => [
                    [
                        'name'      => 'Engineer controlled load shedding and islanding for curtailment',
                        'objective' => 'Ride through grid curtailment with a planned, tested response instead of '
                                     . 'an uncontrolled trip.',
                        'perform' => [
                            'Build the site electrical network model in EnPharChem Cimphony Network Model Management, validated against as-built drawings rather than assumed.',
                            'Classify every load by process criticality with the process engineers: what must never lose power, what can be shed, and in what order.',
                            'Configure the shedding scheme and islanding logic in EnPharChem Microgrid Management System, coordinated with generation capability.',
                            'Integrate monitoring and control through EnPharChem OSI Monarch SCADA and manage on-site generation via EnPharChem OSI Generation Management System.',
                            'Simulate curtailment and islanding scenarios before commissioning, including the worst case of losing the grid at maximum import.',
                            'Test the scheme live under controlled conditions - an untested islanding scheme should be assumed not to work.',
                            'Publish status and readiness through EnPharChem Resilience Portal and record events in EnPharChem OSI CHRONUS Historian.',
                        ],
                        'analyse' => [
                            'Verify the shed sequence keeps the synthesis loop and all safety-critical systems energised in every simulated scenario; a scheme that protects the wrong loads is worse than none.',
                            'Check frequency and voltage transients during simulated islanding against equipment withstand limits - the transition is where islanding actually fails.',
                            'Confirm on-site generation can pick up the retained load within its ramp capability, and identify the largest step it can survive.',
                            'Quantify the production consequence of each shed step, so the sequence reflects process value rather than electrical convenience.',
                            'Compare live test results against simulation and reconcile any difference before relying on the scheme. Illustrative acceptance: successful islanding in test with critical loads retained and transients inside withstand limits.',
                        ],
                        'kpi' => 'A tested islanding and shedding scheme that retains critical load with transients inside equipment limits.',
                    ],
                    [
                        'name'      => 'Dispatch on-site generation and distributed resources economically',
                        'objective' => 'Minimise electricity cost while holding the reserve that resilience '
                                     . 'requires.',
                        'perform' => [
                            'Model generation units, distributed resources and the grid connection with real efficiency curves, ramp rates and limits.',
                            'Load the tariff structure including time-of-use and demand charges, and the curtailment schedule where it is published.',
                            'Optimise dispatch in EnPharChem OSI Energy Management System against total cost, with a reserve constraint reflecting the resilience requirement.',
                            'Manage distributed and renewable resources through EnPharChem OSI Distributed Energy Resource Management System and EnPharChem DER Connect.',
                            'Coordinate distribution-level operation in EnPharChem OSI Advanced Distribution Management System, using EnPharChem Network Maps for situational awareness.',
                            'Report performance through EnPharChem Grid Reporter and EnPharChem Grid Apps.',
                        ],
                        'analyse' => [
                            'Compare optimised against actual dispatch cost and attribute the difference to specific decisions, so the saving is auditable rather than asserted.',
                            'Verify the reserve constraint is respected at all times; a dispatch that is cheapest but leaves no reserve has traded resilience for margin, which is the wrong trade for this plant.',
                            'Separate the demand-charge saving from the energy saving, since they are earned by different behaviour and one can be pursued without the other.',
                            'Check that renewable variability does not compromise the reserve, and size the reserve for the worst plausible variability rather than the average.',
                            'Re-optimise when tariffs or curtailment patterns change, and treat a stale dispatch policy as a cost. Illustrative target: demonstrable cost reduction with the resilience reserve never breached.',
                        ],
                        'kpi' => 'Demonstrable electricity cost reduction with the resilience reserve maintained at all times.',
                    ],
                ],
            ],
        ];
    }
}
