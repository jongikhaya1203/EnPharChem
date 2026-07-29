<?php
/**
 * AIUseCasesPDFBuilder — builds the "AI Use Cases by Module" PDF using MiniPDF.
 * Generates a real PDF binary; no external dependencies required.
 */
require_once __DIR__ . '/MiniPDF.php';

class AIUseCasesPDFBuilder {

    public static function categories() {
        return [
            [
                'name' => 'Process Simulation for Energy',
                'overview' => 'AI/ML augments first-principles thermodynamics with data-driven surrogates, accelerates convergence, predicts safety scenarios, and tunes refining reactor kinetics in real time.',
                'modules' => [
                    ['EnPharChem HYSYS', 'Surrogate Neural Networks; Bayesian Calibration', 'ML-augmented thermodynamic property prediction; AI-accelerated flowsheet convergence; data-driven tuning of EOS parameters.', 'Up to 70% faster steady-state convergence; reduced model re-tuning effort.'],
                    ['Acid Gas Cleaning', 'Gradient-Boosted Trees; LSTM', 'Predicts amine solvent loading, degradation rates, and CO2/H2S slip; flags abnormal absorber performance.', 'Lower solvent make-up costs; reduced corrosion incidents.'],
                    ['EnPharChem HYSYS Crude', 'Deep Neural Networks (TBP)', 'AI-based crude assay characterization; auto-generation of TBP and property curves from limited lab data.', 'Faster crude qualification; better refinery yield predictions.'],
                    ['BLOWDOWN Technology', 'Classification ML; Monte Carlo + ML', 'Risk classification of depressurization scenarios; auto-identification of low-temperature embrittlement risks.', 'Higher PSM compliance; fewer over-conservative designs.'],
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
                'overview' => 'AI optimises site-wide energy, models decarbonization pathways, and forecasts utility demand for industrial sustainability.',
                'modules' => [
                    ['EnPharChem Energy Analyzer', 'AI Pinch Designer; ML Forecasts', 'AI HEN synthesis; utility demand forecasting.', 'Site energy intensity reduction.'],
                    ['EnPharChem Sustainability Pathways', 'Optimization + ML; LCA AI', 'AI decarbonization-pathway optimizer; ML life-cycle inventory.', 'Net-zero roadmap with cost curves.'],
                    ['EnPharChem Utilities Planner', 'LSTM Demand Forecast; AI Scheduling', 'AI utility steam/power demand forecasting and scheduling.', 'Lower utility cost; reliability.'],
                ],
            ],
            [
                'name' => 'Operations Support',
                'overview' => 'Online AI keeps process simulation aligned with plant reality and provides intelligent assistants inside engineering workbooks.',
                'modules' => [
                    ['EnPharChem OnLine', 'Live ML Soft Sensors; Anomaly AI', 'Continuous data reconciliation + AI anomaly detection on live plant streams.', 'Closes simulation-to-plant gap.'],
                    ['EnPharChem Simulation Workbook', 'LLM Excel Assistant', 'AI recommends parameters, explains results, generates formulas inside workbook.', 'Faster engineering studies.'],
                ],
            ],
            [
                'name' => 'Advanced Process Control',
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
                'overview' => 'Closed-loop AI/RL optimization runs against live plant economics to capture real-time benefits.',
                'modules' => [
                    ['EnPharChem GDOT', 'Reinforcement Learning; NN Surrogates; RTO+ML', 'Real-time optimization with AI surrogates of process units; RL closed-loop economic optimization.', 'Continuous margin uplift across the unit.'],
                ],
            ],
            [
                'name' => 'Manufacturing Execution Systems',
                'overview' => 'AI enhances historian intelligence, batch deviation detection, and material balance reconciliation.',
                'modules' => [
                    ['EnPharChem InfoPlus.21', 'AI Tag Anomaly; ML Data Quality', 'Auto-flags drifting tags, frozen signals, and bad data quality.', 'Reliable downstream analytics.'],
                    ['EnPharChem Production Record Manager', 'AI Batch Deviation Detection', 'Detects parameter excursions in eBRs; auto-classifies deviations.', 'Faster QA release.'],
                    ['enPharChemONE Process Explorer', 'AI Trend Anomalies; LLM Search', 'Natural-language search of historian; AI anomaly highlights.', 'Faster troubleshooting.'],
                    ['EnPharChem Production Execution Manager', 'AI Workflow Optimization', 'AI recommends order sequencing and crew assignment.', 'Higher OEE.'],
                    ['EnPharChem Unified Reconciliation & Accounting', 'ML Reconciliation; AI Gross Error', 'Improves data reconciliation via ML weights; auto gross-error detection.', 'Closer mass balance closure.'],
                    ['EnPharChem Unified Movements', 'AI Tank Scheduling', 'Optimizes tank-to-tank movements with AI scheduler.', 'Reduced demurrage and contamination.'],
                    ['EnPharChem Operations Reconciliation', 'ML Yield Accounting', 'AI yield accounting and material balance closure.', 'Trustworthy operations KPIs.'],
                    ['EnPharChem Tank and Operations Manager', 'AI Tank-Level Forecasting', 'Predicts tank levels and ullage; AI alarming.', 'Avoids overflow/run-dry events.'],
                ],
            ],
            [
                'name' => 'Petroleum Supply Chain',
                'overview' => 'AI augments LP/NLP planning with warm-starts, validation, and LLM advisors that explain planning model results to schedulers.',
                'modules' => [
                    ['EnPharChem Unified PIMS', 'AI Warm-Start; ML Model Validation', 'AI seeds LP/NLP solvers; ML detects unrealistic vectors.', 'Faster, more reliable plans.'],
                    ['EnPharChem Unified Scheduling', 'AI/RL Scheduler', 'RL learns near-optimal crude/product schedules.', 'Better vessel-tank-unit alignment.'],
                    ['EnPharChem Unified Multisite', 'AI Multi-Site Coordination', 'AI coordinates multi-refinery plans under shared constraints.', 'Network-level margin gain.'],
                    ['EnPharChem EVA for Unified PIMS', 'LLM Planning Advisor', 'Natural-language explanation of PIMS results and trade-offs.', 'Faster planner onboarding.'],
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
                'overview' => 'AI/ML powers demand forecasting, multi-echelon inventory optimization, and KPI insight generation for chemical supply chains.',
                'modules' => [
                    ['enPharChemONE Supply Chain Management', 'AI Integrated SCM', 'AI orchestration across plan/source/make/deliver.', 'End-to-end SCM resilience.'],
                    ['EnPharChem Scheduler Explorer', 'AI/RL Scheduling', 'RL-based plant scheduling with constraint learning.', 'Higher throughput and OTIF.'],
                    ['EnPharChem Collaborative Demand Manager (SCM)', 'LSTM/Prophet/Bayesian Demand', 'Hierarchical demand forecasting with collaboration.', 'Improved forecast accuracy.'],
                    ['EnPharChem Supply Chain Planner', 'AI Multi-Echelon Optimization', 'AI inventory and replenishment optimization.', 'Lower working capital.'],
                    ['EnPharChem Plant Scheduler Family', 'AI Scheduling', 'AI sequence-dependent scheduling.', 'Reduced changeover loss.'],
                    ['EnPharChem SCM Insights', 'AI KPI Anomaly; LLM Insights', 'Auto-narrated SCM insights and AI anomaly flags.', 'Faster managerial action.'],
                ],
            ],
            [
                'name' => 'Asset Performance Management',
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
                'overview' => 'AI gives industrial data context: semantic enrichment, NLP-based metadata, and ML data-quality scoring.',
                'modules' => [
                    ['EnPharChem Inmation', 'NLP Metadata; AI Semantic Enrichment; ML Quality', 'AI auto-tags industrial data, builds contextual graphs, and scores data quality.', 'Trustworthy data foundation for all AI use cases.'],
                ],
            ],
            [
                'name' => 'Digital Grid Management',
                'overview' => 'AI underpins modern grid: forecasting, DER orchestration, outage prediction, FLISR/VVO automation, and storm resilience.',
                'modules' => [
                    ['EnPharChem Microgrid Management System', 'RL Microgrid Control; AI EMS', 'RL agent optimizes microgrid dispatch with PV/wind/BESS forecasts.', 'Lower energy cost and higher reliability.'],
                    ['EnPharChem OSI Monarch SCADA', 'AI Alarm Management; ML Anomaly', 'AI alarm rationalization; ML SCADA anomaly detection.', 'Reduced operator overload.'],
                    ['EnPharChem OSI Generation Management', 'AI Dispatch; Renewables Forecast', 'AI economic dispatch with wind/solar forecasting.', 'Lower marginal generation cost.'],
                    ['EnPharChem OSI Energy Management', 'Deep-Learning State Estimation', 'AI EMS state estimation and contingency analysis.', 'Higher transmission reliability.'],
                    ['EnPharChem OSI Advanced Distribution Mgmt', 'AI FLISR; ML VVO; Outage AI', 'AI-driven fault location/isolation/service restoration; volt/var optimization.', 'Lower SAIDI/SAIFI.'],
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
    }

    public static function techniqueIndex() {
        return [
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
    }

    public static function build() {
        $pdf = new MiniPDF();
        $W = $pdf->contentWidth();
        $left = $pdf->marginL;
        $generated = date('F j, Y');
        $categories = self::categories();
        $totalModules = 0;
        foreach ($categories as $c) $totalModules += count($c['modules']);

        // === Cover page ===
        // Hero banner
        $pdf->fillRect(0, 0, $pdf->pageW, 230, 13, 40, 71);                  // dark blue
        $pdf->fillRect(0, 200, $pdf->pageW, 30, 13, 110, 253);               // accent strip

        $pdf->setFont('B', 28);
        $pdf->setTextColor(255, 255, 255);
        $title = 'AI Use Cases by Module';
        $tw = $pdf->stringWidth($title);
        $pdf->text(($pdf->pageW - $tw) / 2, 90, $title);

        $pdf->setFont('', 12);
        $sub = 'A detailed catalogue of AI/ML use cases across the EnPharChem platform';
        $sw = $pdf->stringWidth($sub);
        $pdf->text(($pdf->pageW - $sw) / 2, 125, $sub);

        $pdf->setFont('B', 11);
        $meta = sprintf('%d categories  |  %d modules  |  %d AI techniques  |  Generated %s',
            count($categories), $totalModules, count(self::techniqueIndex()), $generated);
        $mw = $pdf->stringWidth($meta);
        $pdf->setTextColor(168, 200, 232);
        $pdf->text(($pdf->pageW - $mw) / 2, 175, $meta);

        $pdf->setTextColor(0, 0, 0);
        $pdf->setY(260);

        // Executive Summary
        $pdf->setFont('B', 14);
        $pdf->setTextColor(13, 40, 71);
        $pdf->text($left, $pdf->getY(), 'Executive Summary');
        $pdf->moveY(8);
        $pdf->line($left, $pdf->getY(), $left + 100, $pdf->getY(), 13, 110, 253, 2);
        $pdf->moveY(12);

        $pdf->setFont('', 10.5);
        $pdf->setTextColor(30, 41, 59);
        $pdf->writeWrapped($left, $W, 14,
            'EnPharChem embeds artificial intelligence and machine learning into every module category - from '
            . 'seismic interpretation and reservoir simulation, through process simulation, exchanger design, FEED, '
            . 'MES, APC, supply chain, asset performance management, to grid management. This document tabulates '
            . 'the principal AI/ML use cases for each module, identifies the underlying technique, and states the '
            . 'expected business value, so that engineers, operators, and trainees can immediately recognise the AI '
            . 'surface area of every tool they use.');
        $pdf->moveY(6);
        $pdf->writeWrapped($left, $W, 14,
            'This catalogue is also a training reference: each row maps a module to the AI technique a learner '
            . 'should understand in order to operate that module safely and to extract value from it. Use it '
            . 'alongside the module-specific training courses available under Training > Courses.');
        $pdf->moveY(14);

        // KPI cards
        $cardW = ($W - 30) / 4;
        $cardH = 56;
        $startY = $pdf->getY();
        $kpiTextColors = [[8, 145, 178], [5, 150, 105], [234, 88, 12], [124, 58, 237]];
        $kpiValues = [count($categories), $totalModules, count(self::techniqueIndex()), '100%'];
        $kpiLabels = ['CATEGORIES', 'MODULES COVERED', 'AI TECHNIQUES', 'AI COVERAGE'];
        for ($i = 0; $i < 4; $i++) {
            $cx = $left + $i * ($cardW + 10);
            $pdf->fillRect($cx, $startY, $cardW, $cardH, 248, 250, 252);
            $pdf->strokeRect($cx, $startY, $cardW, $cardH, 226, 232, 240, 0.6);
            // top accent
            $col = $kpiTextColors[$i];
            $pdf->fillRect($cx, $startY, $cardW, 3, $col[0], $col[1], $col[2]);
            // value
            $pdf->setFont('B', 22);
            $pdf->setTextColor($col[0], $col[1], $col[2]);
            $v = (string)$kpiValues[$i];
            $vw = $pdf->stringWidth($v);
            $pdf->text($cx + ($cardW - $vw) / 2, $startY + 28, $v);
            // label
            $pdf->setFont('B', 7.5);
            $pdf->setTextColor(100, 116, 139);
            $lw = $pdf->stringWidth($kpiLabels[$i]);
            $pdf->text($cx + ($cardW - $lw) / 2, $startY + 47, $kpiLabels[$i]);
        }
        $pdf->setY($startY + $cardH + 16);

        // Table of contents
        $pdf->setFont('B', 14);
        $pdf->setTextColor(13, 40, 71);
        $pdf->text($left, $pdf->getY(), 'Contents');
        $pdf->moveY(8);
        $pdf->line($left, $pdf->getY(), $left + 70, $pdf->getY(), 13, 110, 253, 2);
        $pdf->moveY(10);

        $pdf->setFont('', 10);
        $pdf->setTextColor(30, 41, 59);
        $i = 1;
        foreach ($categories as $cat) {
            $line = sprintf('%2d.  %s  (%d module%s)',
                $i, $cat['name'], count($cat['modules']),
                count($cat['modules']) === 1 ? '' : 's');
            $pdf->ensureSpace(14);
            $pdf->text($left, $pdf->getY(), $line);
            $pdf->moveY(14);
            $i++;
        }

        // === Category sections ===
        $idx = 1;
        foreach ($categories as $cat) {
            $pdf->addPage();
            // Section header bar
            $pdf->fillRect($left, $pdf->getY(), $W, 32, 13, 40, 71);
            $pdf->setFont('B', 13);
            $pdf->setTextColor(255, 255, 255);
            $pdf->text($left + 12, $pdf->getY() + 11, sprintf('%d. %s', $idx, $cat['name']));
            $pdf->setFont('', 9);
            $countLabel = sprintf('%d module%s', count($cat['modules']),
                count($cat['modules']) === 1 ? '' : 's');
            $cw = $pdf->stringWidth($countLabel);
            $pdf->text($left + $W - $cw - 12, $pdf->getY() + 12, $countLabel);
            $pdf->moveY(40);

            // Overview block
            $pdf->fillRect($left, $pdf->getY(), $W, 1, 13, 110, 253);
            $pdf->moveY(6);
            $pdf->setFont('I', 9.5);
            $pdf->setTextColor(71, 85, 105);
            $pdf->writeWrapped($left, $W, 12, $cat['overview']);
            $pdf->moveY(8);

            // Table
            $colWidths = [
                ['width' => $W * 0.20, 'align' => 'L'],   // Module
                ['width' => $W * 0.22, 'align' => 'L'],   // Technique
                ['width' => $W * 0.36, 'align' => 'L'],   // Use case
                ['width' => $W * 0.22, 'align' => 'L'],   // Outcome
            ];
            $pdf->table(
                $left,
                $colWidths,
                ['Module', 'AI / ML Technique', 'AI Use Case', 'Business Outcome'],
                $cat['modules'],
                [
                    'headerBg'    => [13, 40, 71],
                    'headerFg'    => [255, 255, 255],
                    'rowAltBg'    => [244, 247, 251],
                    'borderColor' => [203, 213, 225],
                    'padding'     => 5,
                    'rowMinH'     => 16,
                    'headerFS'    => 9,
                    'bodyFS'      => 8.5,
                    'textColors'  => [
                        [13, 40, 71],     // module (dark)
                        [8, 145, 178],    // technique (cyan)
                        [30, 41, 59],     // use case (slate)
                        [5, 150, 105],    // outcome (green)
                    ],
                ]
            );
            $idx++;
        }

        // === Cross-cutting techniques ===
        $pdf->addPage();
        $pdf->fillRect($left, $pdf->getY(), $W, 32, 13, 40, 71);
        $pdf->setFont('B', 13);
        $pdf->setTextColor(255, 255, 255);
        $pdf->text($left + 12, $pdf->getY() + 11, 'Cross-cutting AI / ML Technique Index');
        $pdf->moveY(46);

        $techCols = [
            ['width' => $W * 0.30, 'align' => 'L'],
            ['width' => $W * 0.70, 'align' => 'L'],
        ];
        $techRows = [];
        foreach (self::techniqueIndex() as $name => $desc) {
            $techRows[] = [$name, $desc];
        }
        $pdf->table(
            $left,
            $techCols,
            ['Technique', 'Description'],
            $techRows,
            [
                'headerBg'    => [13, 40, 71],
                'headerFg'    => [255, 255, 255],
                'rowAltBg'    => [244, 247, 251],
                'borderColor' => [203, 213, 225],
                'padding'     => 6,
                'rowMinH'     => 18,
                'headerFS'    => 9.5,
                'bodyFS'      => 9,
                'textColors'  => [
                    [13, 40, 71],
                    [30, 41, 59],
                ],
            ]
        );

        // Footer note
        $pdf->moveY(20);
        $pdf->setFont('I', 8);
        $pdf->setTextColor(148, 163, 184);
        $footer = 'EnPharChem AI Use Cases  |  Generated ' . $generated . '  |  Confidential - Training & Reference';
        $fw = $pdf->stringWidth($footer);
        $pdf->text($left + ($W - $fw) / 2, $pdf->getY(), $footer);

        return $pdf->output();
    }
}
