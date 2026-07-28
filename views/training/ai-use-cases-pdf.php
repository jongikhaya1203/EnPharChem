<?php
/**
 * EnPharChem Platform - AI Use Cases per Module (PDF)
 * Detailed catalog of AI/ML use cases across all 15 module categories
 */
$generated = date('F j, Y');

$categories = [
    [
        'name' => 'Process Simulation for Energy',
        'icon' => 'fa-bolt',
        'overview' => 'AI/ML augments first-principles thermodynamics with data-driven surrogates, accelerates convergence, predicts safety scenarios, and tunes refining reactor kinetics in real time.',
        'modules' => [
            ['EnPharChem HYSYS', 'Surrogate Neural Networks; Bayesian Calibration', 'ML-augmented thermodynamic property prediction; AI-accelerated flowsheet convergence; data-driven tuning of EOS parameters.', 'Up to 70% faster steady-state convergence; reduced model re-tuning effort.'],
            ['Acid Gas Cleaning', 'Gradient-Boosted Trees; LSTM', 'Predicts amine solvent loading, degradation rates, and CO2/H2S slip; flags abnormal absorber performance.', 'Lower solvent make-up costs; reduced corrosion incidents.'],
            ['EnPharChem HYSYS Crude', 'Deep Neural Networks (TBP)', 'AI-based crude assay characterization; auto-generation of TBP and property curves from limited lab data.', 'Faster crude qualification; better refinery yield predictions.'],
            ['BLOWDOWN Technology', 'Classification ML; Monte-Carlo + ML', 'Risk classification of depressurization scenarios; auto-identification of low-temperature embrittlement risks.', 'Higher PSM compliance; fewer over-conservative designs.'],
            ['Relief Sizing (Energy)', 'Anomaly Detection; Decision Trees', 'AI scenario generator for PSV sizing; detects unrealistic loads and missing scenarios in HAZOP coverage.', 'API 521/520 compliance with audit-ready trace.'],
            ['Sulsim Sulfur Recovery', 'Reinforcement Learning; NN Surrogates', 'Optimizes Claus reactor air ratio; learns optimal tail-gas treatment trajectories.', '>99.9% sulfur recovery; lower SO2 emissions.'],
            ['Activated Economics (Energy)', 'LSTM Time-Series; Cost Indices ML', 'Forecasts equipment/material cost escalation; AI-driven CAPEX/OPEX scenario analysis.', 'More accurate Class 3/4 estimates.'],
            ['Activated Energy Analysis (Energy)', 'Graph NN; AI Pinch Designer', 'AI proposes optimal heat-exchanger networks; predicts hot/cold composite curves from streams.', '10-30% energy reduction via better integration.'],
            ['Activated EDR (Energy)', 'NN Exchanger Surrogates', 'Replaces inner exchanger rating loops with fast NN surrogates inside flowsheet.', '5-10x faster HX-coupled simulations.'],
            ['EnPharChem HYSYS Petroleum Refining', 'Reinforcement Learning; Kinetic ML', 'Tunes FCC, reformer, hydrocracker kinetics to live yields; closes the loop with plant data.', 'Higher margin via yield optimization.'],
            ['Refinery Reactor Models', 'RL Control; Surrogate NN', 'AI agent recommends WHSV, temperature, and H2/HC ratios.', 'Extended catalyst run length.'],
            ['EnPharChem HYSYS Upstream', 'ML Flow Assurance; XGBoost', 'Predicts hydrate, wax, slugging; AI well production decline forecasting.', 'Reduced production downtime.'],
            ['EnPharChem HYSYS Dynamics', 'Recurrent NN Surrogates', 'LSTM dynamic-process surrogates for fast transient studies and OTS.', 'Real-time-capable OTS scenarios.'],
            ['Activated Dynamics', 'AI Operator Advisor (LLM-style)', 'Real-time guidance on abnormal-event response; transient anomaly classification.', 'Faster recovery from upsets.'],
            ['EnPharChem Hybrid Models (Energy)', 'Hybrid First-Principles + ML', 'Native AI/ML hybrid: combines mechanistic skeleton with neural residual.', 'Industrial-grade extrapolation with data.'],
            ['EnPharChem Multi Case (Energy)', 'Bayesian Optimization; AI DOE', 'Active learning across parametric studies; recommends next high-information case.', 'Fewer simulation runs for same insight.'],
            ['EnPharChem Operator Training', 'LLM Coach; Scenario Generator', 'AI generates plausible upset scenarios; LLM answers operator questions in context.', 'Higher trainee competency, lower instructor load.'],
            ['EnPharChem Flare System Analyzer (Energy)', 'CFD Surrogates; Emissions ML', 'NN-based flare radiation/noise contour prediction; AI emissions inventory.', 'Faster siting and permit compliance.'],
        ],
    ],
    [
        'name' => 'Process Simulation for Chemicals',
        'icon' => 'fa-flask',
        'overview' => 'AI accelerates chemical process design through molecular-property neural networks, batch trajectory optimization, and automated documentation via LLMs.',
        'modules' => [
            ['EnPharChem Adsorption', 'Gaussian Process; NN Isotherms', 'AI fits Langmuir/Freundlich/Toth isotherms; optimizes PSA/TSA/VSA cycle times.', 'Higher productivity; lower energy per kg adsorbate.'],
            ['EnPharChem Chromatography', 'CNN Peak Detection; AI Method DOE', 'Detects and deconvolutes peaks; AI gradient and mobile-phase optimization.', 'Reduced method development time.'],
            ['EnPharChem Custom Modeler', 'Symbolic Regression', 'Equation discovery from data to extend custom unit models.', 'Captures novel equipment behavior.'],
            ['EnPharChem Hybrid Models (Chemicals)', 'Hybrid Physics-ML; Auto-DOE', 'Combines mechanistic and data-driven layers; auto experimental design.', 'Faster scale-up; better generalization.'],
            ['EnPharChem Multi Case (Chemicals)', 'Bayesian Sensitivity; Sobol-NN', 'AI sensitivity analysis prioritizes high-impact variables.', 'Sharper sensitivity insight per run.'],
            ['EnPharChem Plus', 'Neural Surrogates; Convergence ML', 'NN surrogates for towers, reactors; learned convergence assistants.', '2-10x simulation throughput.'],
            ['EnPharChem Polymers', 'NN MWD Predictor', 'Predicts molecular-weight distribution from operating conditions.', 'Tighter product quality.'],
            ['EnPharChem Properties', 'Graph Neural Networks (GNN)', 'Molecular GNN property prediction; auto thermo-model selection.', 'Faster property estimation for novel molecules.'],
            ['Batch Modeling in EnPharChem Plus', 'RL Batch Trajectory; PAT-ML', 'Optimizes feed/temperature trajectories; integrates Process Analytical Technology.', 'Improved batch consistency and yield.'],
            ['Distillation Modeling in EnPharChem Plus', 'NN Tray Hydraulics; AI Control', 'AI tray-loading prediction; ML soft sensors for product purity.', 'Higher recovery, less reboiler energy.'],
            ['Relief Sizing (Chemicals)', 'Scenario ML; Anomaly Detection', 'AI scenario classifier for runaway-reaction PSVs.', 'DIERS-compliant sizing assistance.'],
            ['Solids Modeling', 'Computer Vision; DEM Surrogate', 'CV-based particle-size distribution; NN surrogate for DEM solids simulation.', 'Faster solids design cycles.'],
            ['Activated Economics (Chemicals)', 'LSTM Cost Forecasting', 'AI-driven economic evaluation under inflation scenarios.', 'Realistic NPV/IRR ranges.'],
            ['Activated Energy Analysis (Chemicals)', 'Graph NN Heat Networks', 'AI proposes utility integration topology.', 'Lower utility cost.'],
            ['Activated EDR (Chemicals)', 'NN Exchanger Surrogate', 'Embeds AI HX rating in chemical flowsheet.', 'Fast iteration on HX-bound designs.'],
            ['EnPharChem Plus Dynamics', 'AI Controller Tuning', 'Auto-tunes PID/MPC for chemical dynamic models.', 'Better controllability scoring.'],
            ['EnPharChem Process Manuals', 'LLM Document Generation', 'Auto-generates operating manuals, P&IDs narratives, and basis-of-design.', 'Cuts documentation time by 60-80%.'],
            ['EnPharChem Flare System Analyzer (Chemicals)', 'CFD ML Surrogates', 'AI flare network rating with emissions inventory.', 'Faster compliance studies.'],
        ],
    ],
    [
        'name' => 'Exchanger Design & Rating',
        'icon' => 'fa-exchange-alt',
        'overview' => 'AI replaces costly CFD/rigorous correlations with neural surrogates and predicts fouling/efficiency over operating life.',
        'modules' => [
            ['EnPharChem Air Cooled Exchanger', 'NN Fan Power; Weather ML', 'AI fan-speed optimization with ambient-temperature forecasting.', 'Lower aux-power consumption.'],
            ['EnPharChem Fired Heater', 'NN Combustion Model; AI Efficiency', 'Predicts firebox temperature, NOx, and efficiency from fuel mix.', 'Lower fuel cost; reduced emissions.'],
            ['EnPharChem Plate Exchanger', 'Fouling-Rate ML', 'Predicts plate fouling under variable services; recommends cleaning intervals.', 'Less unplanned shutdown.'],
            ['EnPharChem Plate Fin Exchanger', 'NN Cryogenic Design', 'AI design assistant for brazed aluminum heat exchangers.', 'Optimized cryogenic LNG service.'],
            ['EnPharChem Shell & Tube Exchanger', 'TEMA Design AI; Fouling ML', 'AI selects TEMA type; predicts fouling factors; auto pass-arrangement.', 'Faster shortlisting of viable designs.'],
            ['EnPharChem Shell & Tube Mechanical', 'AI ASME Compliance Check', 'Auto stress/vibration checks; flags code non-conformance.', 'Reduced redesign iterations.'],
            ['EnPharChem Coil Wound Exchanger', 'NN Cryogenic Thermal Surrogate', 'AI thermal model for LNG main exchangers.', 'Faster what-if studies.'],
        ],
    ],
    [
        'name' => 'Concurrent FEED',
        'icon' => 'fa-drafting-compass',
        'overview' => 'Generative AI proposes 3D plant layouts; LLMs auto-draft engineering deliverables; ML forecasts cost escalation through FEED.',
        'modules' => [
            ['EnPharChem Fidelis', 'AI Cost-Process Linkage', 'Live link between simulation changes and CAPEX impact via ML cost models.', 'Faster concurrent FEED iterations.'],
            ['EnPharChem Capital Cost Estimator', 'XGBoost; ML Location Factors', 'AI equipment-cost regression; ML location/labor factors.', 'Tighter Class 3 estimates.'],
            ['EnPharChem In-Plant Cost Estimator', 'ML Revamp Forecasting', 'Predicts revamp cost from brownfield constraints.', 'Better turnaround budgeting.'],
            ['EnPharChem Process Economic Analyzer', 'LSTM Economics; Scenario AI', 'AI NPV/IRR/payback forecasting under multiple price decks.', 'Risk-adjusted investment decisions.'],
            ['EnPharChem OptiPlant 3D Layout', 'Generative AI Layout; Clash AI', 'GAN-based layout proposals; AI clash detection across disciplines.', 'Shorter FEED schedules.'],
            ['EnPharChem OptiRouter', 'Reinforcement Learning Pipe Routing', 'RL agent learns rule-compliant pipe routes minimizing length and supports.', 'Faster, cheaper pipe routing.'],
            ['EnPharChem Basic Engineering', 'LLM Auto P&ID; Datasheet AI', 'LLM drafts P&ID narratives, datasheets, and SOR.', 'Cuts basic engineering by 40-60%.'],
        ],
    ],
    [
        'name' => 'Subsurface Science & Engineering',
        'icon' => 'fa-mountain',
        'overview' => 'Deep learning transforms seismic processing, log interpretation, geological modelling, and reservoir simulation across the upstream lifecycle.',
        'modules' => [
            ['EnPharChem Subsurface Intelligence (ESI)', 'AI Data Fusion; ML Insights', 'Cross-domain subsurface fusion: seismic + well + production via ML.', 'Faster decision turnaround.'],
            ['EnPharChem Echos', 'Deep Learning Denoise; CNN Velocity', 'AI seismic processing; CNN velocity-model building.', 'Cleaner images; less manual QC.'],
            ['EnPharChem EarthStudy 360', 'Azimuthal Deep Learning', 'AI full-azimuth imaging and anisotropy detection.', 'Sharper reservoir characterization.'],
            ['EnPharChem GeoDepth', 'Deep-Learning Migration', 'AI velocity update; learning-based RTM components.', 'Faster depth imaging.'],
            ['EnPharChem SeisEarth', 'CNN Horizon/Fault Picking', 'Deep-learning auto-picking of horizons, faults, salt bodies.', '70-90% interpretation time saved.'],
            ['EnPharChem Geolog', 'ML Petrophysics; NN Log Synthesis', 'Predicts missing logs; AI lithology/facies classification.', 'Consistent petrophysical analysis.'],
            ['EnPharChem RMS', 'AI Geostatistics; ML Property Modeling', 'NN-driven property modeling between wells.', 'Better history-match starting points.'],
            ['EnPharChem SKUA', 'Deep-Learning Geomodels', 'AI structural framework building.', 'Faster geological model build.'],
            ['EnPharChem OpsLink', 'Real-Time Anomaly Detection', 'AI monitors live field telemetry; detects equipment/well anomalies.', 'Reduced NPT (non-productive time).'],
            ['EnPharChem Tempest', 'RL Well Control; ML History Matching', 'RL learns optimal well operating strategies; AI assisted history match.', 'Improved recovery factor.'],
            ['EnPharChem Epos', 'AI Risk Scoring; Portfolio ML', 'AI exploration risk; ML portfolio optimization under uncertainty.', 'Better drilling capital allocation.'],
        ],
    ],
    [
        'name' => 'Energy & Utilities Optimization',
        'icon' => 'fa-leaf',
        'overview' => 'AI optimises site-wide energy, models decarbonization pathways, and forecasts utility demand for industrial sustainability.',
        'modules' => [
            ['EnPharChem Energy Analyzer', 'AI Pinch Designer; ML Forecasts', 'AI HEN synthesis; utility demand forecasting.', 'Site energy intensity reduction.'],
            ['EnPharChem Strategic Planning for Sustainability Pathways', 'Optimization + ML; LCA AI', 'AI decarbonization-pathway optimizer; ML life-cycle inventory.', 'Net-zero roadmap with cost curves.'],
            ['EnPharChem Utilities Planner', 'LSTM Demand Forecast; AI Scheduling', 'AI utility steam/power demand forecasting and scheduling.', 'Lower utility cost; reliability.'],
        ],
    ],
    [
        'name' => 'Operations Support',
        'icon' => 'fa-desktop',
        'overview' => 'Online AI keeps process simulation aligned with plant reality and provides intelligent assistants inside engineering workbooks.',
        'modules' => [
            ['EnPharChem OnLine', 'Live ML Soft Sensors; Anomaly AI', 'Continuous data reconciliation + AI anomaly detection on live plant streams.', 'Closes simulation-to-plant gap.'],
            ['EnPharChem Simulation Workbook', 'LLM Excel Assistant', 'AI recommends parameters, explains results, generates formulas inside workbook.', 'Faster engineering studies.'],
        ],
    ],
    [
        'name' => 'Advanced Process Control',
        'icon' => 'fa-sliders-h',
        'overview' => 'AI elevates DMC, nonlinear MPC, and soft sensors with automated tuning, deep learning models, and natural-language advisory.',
        'modules' => [
            ['EnPharChem DMC3', 'AI MPC Tuning; ML Model Adaptation', 'Auto-tunes controller; adapts step models from plant data.', 'Sustained APC benefit capture.'],
            ['EnPharChem Virtual Advisor (EVA) for DMC3', 'LLM Advisor', 'Natural-language Q&A on APC performance, root cause, and tuning.', 'Lower APC engineer load.'],
            ['EnPharChem DMC3 Builder', 'AI System Identification', 'NN-based ARX/NARX identification; auto step-test design.', 'Faster controller commissioning.'],
            ['EnPharChem Inferential Qualities', 'Deep-Learning Soft Sensors', 'CNN/LSTM soft sensors for online product quality.', 'Reduced lab dependency.'],
            ['EnPharChem Nonlinear Controller', 'Neural NMPC', 'NN-based nonlinear MPC for strongly nonlinear units.', 'Tighter quality control.'],
            ['EnPharChem Transition Management', 'Reinforcement Learning Transitions', 'RL learns optimal grade-changeover trajectories.', 'Less off-spec product.'],
            ['EnPharChem Watch Performance Monitor', 'AI KPI Anomaly Detection', 'Detects controller degradation, valve issues, and CV drift.', 'Sustained APC service factor.'],
        ],
    ],
    [
        'name' => 'Dynamic Optimization',
        'icon' => 'fa-chart-line',
        'overview' => 'Closed-loop AI/RL optimization runs against live plant economics to capture real-time benefits.',
        'modules' => [
            ['EnPharChem GDOT', 'Reinforcement Learning; NN Surrogates; RTO+ML', 'Real-time optimization with AI surrogates of process units; RL closed-loop economic optimization.', 'Continuous margin uplift across the unit.'],
        ],
    ],
    [
        'name' => 'Manufacturing Execution Systems',
        'icon' => 'fa-industry',
        'overview' => 'AI enhances historian intelligence, batch deviation detection, and material balance reconciliation.',
        'modules' => [
            ['EnPharChem InfoPlus.21', 'AI Tag Anomaly; ML Data Quality', 'Auto-flags drifting tags, frozen signals, and bad data quality.', 'Reliable downstream analytics.'],
            ['EnPharChem Production Record Manager', 'AI Batch Deviation Detection', 'Detects parameter excursions in eBRs; auto-classifies deviations.', 'Faster QA release.'],
            ['enPharChemONE Process Explorer', 'AI Trend Anomalies; LLM Search', 'Natural-language search of historian; AI anomaly highlights.', 'Faster troubleshooting.'],
            ['EnPharChem Production Execution Manager', 'AI Workflow Optimization', 'AI recommends order sequencing and crew assignment.', 'Higher OEE.'],
            ['EnPharChem Unified Reconciliation and Accounting', 'ML Reconciliation; AI Gross Error', 'Improves data reconciliation via ML weights; auto gross-error detection.', 'Closer mass balance closure.'],
            ['EnPharChem Unified Movements', 'AI Tank Scheduling', 'Optimizes tank-to-tank movements with AI scheduler.', 'Reduced demurrage and contamination.'],
            ['EnPharChem Operations Reconciliation and Accounting', 'ML Yield Accounting', 'AI yield accounting and material balance closure.', 'Trustworthy operations KPIs.'],
            ['EnPharChem Tank and Operations Manager', 'AI Tank-Level Forecasting', 'Predicts tank levels and ullage; AI alarming.', 'Avoids overflow/run-dry events.'],
        ],
    ],
    [
        'name' => 'Petroleum Supply Chain',
        'icon' => 'fa-oil-can',
        'overview' => 'AI augments LP/NLP planning with warm-starts, validation, and LLM advisors that explain planning model results to schedulers.',
        'modules' => [
            ['EnPharChem Unified PIMS', 'AI Warm-Start; ML Model Validation', 'AI seeds LP/NLP solvers; ML detects unrealistic vectors.', 'Faster, more reliable plans.'],
            ['EnPharChem Unified Scheduling', 'AI/RL Scheduler', 'RL learns near-optimal crude/product schedules.', 'Better vessel-tank-unit alignment.'],
            ['EnPharChem Unified Multisite', 'AI Multi-Site Coordination', 'AI coordinates multi-refinery plans under shared constraints.', 'Network-level margin gain.'],
            ['EnPharChem Virtual Advisor (EVA) for Unified PIMS', 'LLM Planning Advisor', 'Natural-language explanation of PIMS results and trade-offs.', 'Faster planner onboarding.'],
            ['EnPharChem Verify for Planning', 'AI Model QA', 'ML detects stale yields, broken bounds, and structural issues.', 'Higher plan-vs-actual fidelity.'],
            ['EnPharChem PIMS-AO', 'Advanced AI Optimization', 'AI successive-LP heuristics and integer-cut suggestions.', 'Better optimum on tough cases.'],
            ['EnPharChem Assay Management', 'AI Assay Characterization', 'Auto-builds crude assays from sparse lab data.', 'Faster new-crude qualification.'],
            ['EnPharChem Petroleum Scheduler', 'AI Operations Scheduler', 'Detailed scheduling with AI conflict resolution.', 'Reduced demurrage and giveaway.'],
            ['EnPharChem Refinery Multi-Blend Optimizer', 'ML Blend Quality Prediction', 'NN blend property models for non-linear quality interactions.', 'Less giveaway, more spec compliance.'],
            ['EnPharChem Petroleum Supply Chain Planner', 'AI E2E Planning', 'AI-supported crude-to-product chain planning under uncertainty.', 'Resilient supply chain plans.'],
            ['EnPharChem Collaborative Demand Manager', 'LSTM Demand Forecasting', 'AI demand sensing for petroleum products.', 'Better S&OP accuracy.'],
        ],
    ],
    [
        'name' => 'Supply Chain Management',
        'icon' => 'fa-truck',
        'overview' => 'AI/ML powers demand forecasting, multi-echelon inventory optimization, and KPI insight generation for chemical supply chains.',
        'modules' => [
            ['enPharChemONE Supply Chain Management', 'AI Integrated SCM', 'AI orchestration across plan/source/make/deliver.', 'End-to-end SCM resilience.'],
            ['EnPharChem Scheduler Explorer', 'AI/RL Scheduling', 'RL-based plant scheduling with constraint learning.', 'Higher throughput and OTIF.'],
            ['EnPharChem Collaborative Demand Manager (SCM)', 'LSTM/Prophet/Bayesian Demand', 'Hierarchical demand forecasting with collaboration.', 'Improved forecast accuracy.'],
            ['EnPharChem Supply Chain Planner', 'AI Multi-Echelon Optimization', 'AI inventory and replenishment optimization.', 'Lower working capital.'],
            ['EnPharChem Plant Scheduler Family', 'AI Scheduling', 'AI sequence-dependent scheduling.', 'Reduced changeover loss.'],
            ['EnPharChem Supply Chain Management Insights', 'AI KPI Anomaly; LLM Insights', 'Auto-narrated SCM insights and AI anomaly flags.', 'Faster managerial action.'],
        ],
    ],
    [
        'name' => 'Asset Performance Management',
        'icon' => 'fa-heartbeat',
        'overview' => 'Native AI/ML is the heart of APM: pattern recognition for failure prediction, multivariate analytics, and chemometric data analysis.',
        'modules' => [
            ['EnPharChem Mtell', 'ML Pattern Recognition (Failure Agents)', 'Learns failure signatures and predicts equipment breakdowns weeks ahead.', '20-40% maintenance cost reduction.'],
            ['EnPharChem ProMV', 'PCA/PLS Multivariate Analytics', 'Multivariate statistical process and batch monitoring with contribution plots.', 'Early quality and process deviation alarms.'],
            ['EnPharChem Process Pulse', 'Real-Time Anomaly AI', 'Live anomaly detection across operating envelopes.', 'Prevented unplanned shutdowns.'],
            ['EnPharChem Unscrambler', 'Chemometrics; Neural Networks; DOE', 'Multivariate calibration, classification, and design of experiments.', 'Faster R&D, better PAT models.'],
        ],
    ],
    [
        'name' => 'Industrial Data Fabric',
        'icon' => 'fa-database',
        'overview' => 'AI gives industrial data context: semantic enrichment, NLP-based metadata, and ML data-quality scoring.',
        'modules' => [
            ['EnPharChem Inmation', 'NLP Metadata; AI Semantic Enrichment; ML Quality', 'AI auto-tags industrial data, builds contextual graphs, and scores data quality.', 'Trustworthy data foundation for all AI use cases.'],
        ],
    ],
    [
        'name' => 'Digital Grid Management',
        'icon' => 'fa-plug',
        'overview' => 'AI underpins modern grid: forecasting, DER orchestration, outage prediction, FLISR/VVO automation, and storm resilience.',
        'modules' => [
            ['EnPharChem Microgrid Management System', 'RL Microgrid Control; AI EMS', 'RL agent optimizes microgrid dispatch with PV/wind/BESS forecasts.', 'Lower energy cost and higher reliability.'],
            ['EnPharChem OSI Monarch SCADA', 'AI Alarm Management; ML Anomaly', 'AI alarm rationalization; ML SCADA anomaly detection.', 'Reduced operator overload.'],
            ['EnPharChem OSI Generation Management', 'AI Dispatch; Renewables Forecast', 'AI economic dispatch with wind/solar forecasting.', 'Lower marginal generation cost.'],
            ['EnPharChem OSI Energy Management', 'Deep-Learning State Estimation', 'AI EMS state estimation and contingency analysis.', 'Higher transmission reliability.'],
            ['EnPharChem OSI Advanced Distribution Management', 'AI FLISR; ML VVO; Outage AI', 'AI-driven fault location/isolation/service restoration; volt/var optimization.', 'Lower SAIDI/SAIFI.'],
            ['EnPharChem OSI DER Management', 'AI DER Forecasting; RL Aggregation', 'AI forecasts and orchestrates distributed energy resources.', 'Higher DER hosting capacity.'],
            ['EnPharChem OSI CHRONUS Historian', 'AI Grid Analytics', 'AI analytics on grid historian (event detection, classification).', 'Operational intelligence for grids.'],
            ['EnPharChem OSI Continua Pipeline Management', 'ML Leak Detection; AI Batch Tracking', 'AI leak detection and batch tracking on pipelines.', 'Faster leak response, better integrity.'],
            ['EnPharChem Cimphony Network Model Management', 'AI CIM Validation', 'AI validates CIM network models for consistency.', 'Cleaner model handoff to operations.'],
            ['EnPharChem Grid Apps', 'ML DLR; AI Hosting Capacity; LSTM Load', 'Dynamic line rating, hosting-capacity ML, AI load forecasting.', 'More headroom on existing assets.'],
            ['EnPharChem Grid Reporter', 'LLM Regulatory Reports', 'LLM-generated regulatory and operational reports.', 'Compliance with less effort.'],
            ['EnPharChem DER Connect', 'AI DER Orchestration', 'AI DER interconnection and communications optimization.', 'Faster DER onboarding.'],
            ['EnPharChem Network Maps', 'AI Geospatial Analytics', 'AI geospatial analytics across grid assets.', 'Smarter network planning.'],
            ['EnPharChem Resilience Portal', 'AI Storm/Outage Prediction', 'AI storm-impact forecasting and outage prediction.', 'Higher grid resilience.'],
        ],
    ],
];

$totalModules = 0;
foreach ($categories as $c) { $totalModules += count($c['modules']); }

$techniqueIndex = [
    'Hybrid Physics-ML' => 'Combines first-principles equations with neural-net residual learning. Used in HYSYS Hybrid Models, EnPharChem Plus Hybrid Models, APM, ESI.',
    'Surrogate Neural Networks' => 'Fast NN replacements for expensive rigorous unit operations (towers, exchangers, reactors).',
    'LSTM / Recurrent Networks' => 'Time-series forecasting (demand, equipment health, dynamic process transients, economics).',
    'Reinforcement Learning' => 'Sequential decision-making: dispatch, scheduling, grade transitions, RTO, well control, microgrid.',
    'CNN / Deep Learning' => 'Spatial pattern recognition: seismic interpretation, particle imaging, flare CFD surrogates.',
    'Graph Neural Networks' => 'Molecular property prediction; heat-exchanger network synthesis.',
    'Multivariate Statistics (PCA/PLS)' => 'Batch and continuous-process monitoring (ProMV, Unscrambler).',
    'Anomaly Detection' => 'Catches drift, gross errors, sensor failures, abnormal operation.',
    'LLMs (Large Language Models)' => 'Documentation generation, NL Q&A assistants, regulatory reports, EVA-style advisors.',
    'Bayesian Optimization / Active Learning' => 'Picks the next most-informative simulation case or experiment.',
    'Symbolic Regression' => 'Discovers analytical equations from data inside custom modelers.',
    'Generative AI (GANs/Diffusion)' => 'Generative plant layouts, scenario synthesis.',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EnPharChem - AI Use Cases by Module</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        @page {
            size: A4;
            margin: 14mm 11mm 14mm 11mm;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: #1a1a2e;
            background: #ffffff;
            font-size: 10.5px;
            line-height: 1.45;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .print-bar {
            position: fixed;
            top: 0; left: 0; right: 0;
            background: linear-gradient(135deg, #0f1117, #1a1d23);
            padding: 12px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            z-index: 9999;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        }
        .print-bar .brand { color: #fff; font-weight: 700; font-size: 16px; }
        .print-bar .brand span { color: #0dcaf0; }
        .print-bar .actions { display: flex; gap: 10px; }
        .print-bar .btn-pdf {
            padding: 8px 22px; border-radius: 8px; font-weight: 600;
            font-size: 13px; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-download { background: #0d6efd; color: #fff; }
        .btn-download:hover { background: #0a58ca; }
        .btn-back { background: rgba(255,255,255,0.1); color: #fff; border: 1px solid rgba(255,255,255,0.2); }
        .btn-back:hover { background: rgba(255,255,255,0.2); }
        @media print { .print-bar { display: none !important; } .report-body { padding-top: 0 !important; } }

        .report-body { padding: 78px 6mm 10mm 6mm; max-width: 1100px; margin: 0 auto; }

        .cover-header {
            background: linear-gradient(135deg, #0a1628 0%, #0d2847 50%, #0f3460 100%);
            color: #fff;
            padding: 40px 36px;
            text-align: center;
            margin-bottom: 26px;
            border-radius: 14px;
        }
        .cover-logo {
            width: 70px; height: 70px;
            background: linear-gradient(135deg, #0d6efd, #0dcaf0);
            border-radius: 18px; display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 26px; font-weight: 800; color: #fff;
            margin-bottom: 14px;
        }
        .cover-title { font-size: 30px; font-weight: 800; margin-bottom: 6px; letter-spacing: -0.5px; }
        .cover-subtitle { font-size: 14px; color: #a8c8e8; margin-bottom: 20px; }
        .cover-meta {
            display: inline-flex; gap: 28px;
            font-size: 11px; color: #7fb3d8;
            border-top: 1px solid rgba(255,255,255,0.15);
            padding-top: 14px; margin-top: 8px;
        }
        .cover-meta strong { color: #fff; }

        .summary-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 26px; }
        .summary-card { border: 1px solid #e2e8f0; border-radius: 10px; padding: 14px; text-align: center; }
        .summary-card .value { font-size: 26px; font-weight: 800; }
        .summary-card .label { font-size: 10px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600; }
        .color-cyan { color: #0891b2; border-top: 3px solid #0891b2; }
        .color-green { color: #059669; border-top: 3px solid #059669; }
        .color-orange { color: #ea580c; border-top: 3px solid #ea580c; }
        .color-purple { color: #7c3aed; border-top: 3px solid #7c3aed; }

        .section { margin-bottom: 22px; }
        .section-title {
            font-size: 15px; font-weight: 700; color: #0d2847;
            margin-bottom: 10px; padding-bottom: 6px;
            border-bottom: 3px solid #0d6efd;
            display: flex; align-items: center; gap: 8px;
        }
        .section-title i { color: #0d6efd; }

        .cat-card {
            border: 1px solid #e2e8f0; border-radius: 10px;
            margin-bottom: 18px;
            overflow: hidden;
            page-break-inside: avoid;
        }
        .cat-header {
            background: linear-gradient(135deg, #0d2847, #1a4d8c);
            color: #fff;
            padding: 12px 16px;
            display: flex; justify-content: space-between; align-items: center;
        }
        .cat-header .cat-name { font-size: 14px; font-weight: 700; display: flex; gap: 8px; align-items: center; }
        .cat-header .cat-name i { color: #0dcaf0; }
        .cat-header .cat-count { font-size: 11px; background: rgba(13,202,240,0.2); padding: 4px 10px; border-radius: 12px; }
        .cat-overview { padding: 10px 16px; background: #f1f5f9; font-size: 10.5px; color: #334155; border-bottom: 1px solid #e2e8f0; }

        table.ai-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.ai-table th {
            background: #f8fafc; color: #0d2847; text-align: left;
            padding: 8px 10px; font-weight: 700; font-size: 9.5px;
            border-bottom: 2px solid #cbd5e1; text-transform: uppercase; letter-spacing: 0.3px;
        }
        table.ai-table td {
            padding: 8px 10px; border-bottom: 1px solid #e2e8f0; vertical-align: top;
        }
        table.ai-table tr:nth-child(even) td { background: #fafbfc; }
        table.ai-table td.mod { width: 18%; font-weight: 600; color: #0d2847; }
        table.ai-table td.tech { width: 22%; color: #0891b2; }
        table.ai-table td.use { width: 38%; color: #1e293b; }
        table.ai-table td.val { width: 22%; color: #059669; font-style: italic; }

        .tech-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .tech-item {
            border: 1px solid #e2e8f0; border-radius: 8px; padding: 10px 12px;
            font-size: 10.5px;
        }
        .tech-item strong { color: #0d2847; display: block; margin-bottom: 4px; }
        .tech-item span { color: #475569; }

        .footer { text-align: center; font-size: 9px; color: #94a3b8; margin-top: 24px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
    </style>
</head>
<body>

<div class="print-bar">
    <div class="brand">EnPharChem <span>AI Use Cases</span></div>
    <div class="actions">
        <a href="/enpharchem/training" class="btn-pdf btn-back"><i class="fas fa-arrow-left"></i> Back to Training</a>
        <button class="btn-pdf btn-download" onclick="window.print()"><i class="fas fa-file-pdf"></i> Save as PDF</button>
    </div>
</div>

<div class="report-body">

    <!-- Cover -->
    <div class="cover-header">
        <div class="cover-logo">AI</div>
        <div class="cover-title">AI Use Cases by Module</div>
        <div class="cover-subtitle">A detailed catalog of artificial intelligence and machine learning use cases across the EnPharChem platform</div>
        <div class="cover-meta">
            <div><strong><?= count($categories) ?></strong> Categories</div>
            <div><strong><?= $totalModules ?></strong> Modules</div>
            <div><strong>Generated</strong> <?= htmlspecialchars($generated) ?></div>
            <div><strong>Audience</strong> Engineers & Trainees</div>
        </div>
    </div>

    <!-- Summary -->
    <div class="summary-grid">
        <div class="summary-card color-cyan"><div class="value"><?= count($categories) ?></div><div class="label">Module Categories</div></div>
        <div class="summary-card color-green"><div class="value"><?= $totalModules ?></div><div class="label">Modules Covered</div></div>
        <div class="summary-card color-orange"><div class="value"><?= count($techniqueIndex) ?></div><div class="label">AI/ML Techniques</div></div>
        <div class="summary-card color-purple"><div class="value">100%</div><div class="label">Module AI Coverage</div></div>
    </div>

    <!-- Executive intro -->
    <div class="section">
        <div class="section-title"><i class="fas fa-lightbulb"></i> Executive Summary</div>
        <p style="margin-bottom: 8px;">
            EnPharChem embeds artificial intelligence and machine learning into every module category &mdash; from
            seismic interpretation and reservoir simulation, through process simulation, exchanger design, FEED, MES,
            APC, supply chain, asset performance management, to grid management. This document tabulates the principal
            AI/ML use cases for each module, identifies the underlying technique, and states the expected business
            value, so that engineers, operators, and trainees can immediately recognise the AI surface area of every
            tool they use.
        </p>
        <p>
            This catalog is also a training reference: each row maps a module to the AI technique a learner should
            understand in order to operate that module safely and to extract value from it. Use it alongside the
            module-specific training courses available under <em>Training &rarr; Courses</em>.
        </p>
    </div>

    <!-- One card per category -->
    <?php foreach ($categories as $cat): ?>
    <div class="cat-card">
        <div class="cat-header">
            <div class="cat-name"><i class="fas <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['name']) ?></div>
            <div class="cat-count"><?= count($cat['modules']) ?> module<?= count($cat['modules']) === 1 ? '' : 's' ?></div>
        </div>
        <div class="cat-overview"><?= htmlspecialchars($cat['overview']) ?></div>
        <table class="ai-table">
            <thead>
                <tr>
                    <th>Module</th>
                    <th>AI / ML Technique</th>
                    <th>AI Use Case</th>
                    <th>Business Outcome</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cat['modules'] as $m): ?>
                <tr>
                    <td class="mod"><?= htmlspecialchars($m[0]) ?></td>
                    <td class="tech"><?= htmlspecialchars($m[1]) ?></td>
                    <td class="use"><?= htmlspecialchars($m[2]) ?></td>
                    <td class="val"><?= htmlspecialchars($m[3]) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <!-- Techniques index -->
    <div class="section">
        <div class="section-title"><i class="fas fa-brain"></i> Cross-cutting AI/ML Technique Index</div>
        <div class="tech-grid">
            <?php foreach ($techniqueIndex as $name => $desc): ?>
            <div class="tech-item">
                <strong><?= htmlspecialchars($name) ?></strong>
                <span><?= htmlspecialchars($desc) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="footer">
        EnPharChem AI Use Cases &middot; Generated <?= htmlspecialchars($generated) ?> &middot; Confidential &mdash; Training &amp; Reference
    </div>

</div>

</body>
</html>
