<?php
/**
 * EnPharChem - Module Documentation Content Generator
 * ---------------------------------------------------
 * Derives per-module marketing feature bullets and how-to task walkthroughs
 * from the module registry (name, description, category, licence tier).
 *
 * The workflow steps describe the actual module workspace shipped in
 * views/modules/workspace.php — the Overview / Simulation / Configuration /
 * Results / Documentation tabs and the fields each one exposes — so the
 * How-To Manual matches what a user really sees on screen.
 *
 * Output is seeded into the database (modules.features + module_tasks) by
 * MarketingDocsController::seedModuleDocs().
 */

class ModuleDocContent
{
    /**
     * Per-category domain profile.
     *   artefact  - the primary thing an engineer builds in this category
     *   inputs    - typical input data
     *   outputs   - typical deliverable
     *   verbs     - domain capability phrases used for feature bullets
     *   task      - the category's signature task (title + steps)
     */
    private static function profiles()
    {
        return [
            'process-sim-energy' => [
                'artefact' => 'process flowsheet',
                'inputs'   => 'Feed composition, temperature, pressure and flow rate',
                'outputs'  => 'Converged heat and material balance with stream properties',
                'verbs'    => [
                    'Rigorous vapour-liquid equilibrium using Peng-Robinson and SRK equations of state',
                    'Sequential-modular flowsheet convergence with configurable tolerance',
                    'Heat and material balance closure across all unit operations',
                    'Stream property tables covering enthalpy, entropy and phase fractions',
                    'Case-study sweeps over feed conditions and operating set points',
                ],
                'task' => [
                    'title'     => 'Build and converge a process flowsheet',
                    'objective' => 'Define the feed, select a property package and solve the flowsheet to a converged heat and material balance.',
                    'inputs'    => 'Component list, feed T/P/flow, thermodynamic model',
                    'outputs'   => 'Converged stream table and unit duties',
                    'steps'     => [
                        'Open the <strong>Simulation</strong> tab and give the run a descriptive name, then pick the owning project.',
                        'Enter the component list in <strong>Components Selection</strong> as a comma-separated list (for example <code>Methane, Ethane, Propane</code>).',
                        'Choose the <strong>Thermodynamic Model</strong>. Peng-Robinson suits hydrocarbon and gas-processing systems; NRTL or UNIQUAC suit polar and azeotropic mixtures.',
                        'Set the feed <strong>Temperature (K)</strong>, <strong>Pressure (kPa)</strong> and <strong>Flow Rate (kg/h)</strong>.',
                        'Press <strong>Create &amp; Run Simulation</strong>. The run is created against the project and executed immediately.',
                        'When the status reaches <em>completed</em>, open the <strong>Results</strong> tab to read the temperature profile, pressure drop and composition charts.',
                    ],
                    'tip' => 'If the flowsheet fails to converge, raise Max Iterations on the Configuration tab before loosening the tolerance — a looser tolerance hides imbalance rather than fixing it.',
                ],
            ],
            'process-sim-chemicals' => [
                'artefact' => 'reaction and separation model',
                'inputs'   => 'Component slate, reaction stoichiometry and conversion, column specifications',
                'outputs'  => 'Product composition, conversion and separation performance',
                'verbs'    => [
                    'Activity-coefficient models (NRTL, UNIQUAC, Wilson) for strongly non-ideal mixtures',
                    'Reactor conversion and yield modelling with user-defined stoichiometry',
                    'Rigorous and shortcut distillation with key-component recovery specifications',
                    'Azeotrope detection and solvent screening for difficult separations',
                    'Batch and continuous operating mode support',
                ],
                'task' => [
                    'title'     => 'Model a reaction and separation train',
                    'objective' => 'Specify the chemistry and the downstream separation, then solve for product purity and recovery.',
                    'inputs'    => 'Component slate, reaction conversion, column key components',
                    'outputs'   => 'Product purity, recovery and reboiler/condenser duties',
                    'steps'     => [
                        'On the <strong>Simulation</strong> tab, name the run and select the project it belongs to.',
                        'List every component that appears in the feed, the products and any solvent in <strong>Components Selection</strong>.',
                        'Select an activity-coefficient <strong>Thermodynamic Model</strong> (NRTL, UNIQUAC or Wilson) when the mixture is polar or forms azeotropes.',
                        'Enter the reactor inlet <strong>Temperature (K)</strong> and <strong>Pressure (kPa)</strong>, and the feed <strong>Flow Rate (kg/h)</strong>.',
                        'Submit with <strong>Create &amp; Run Simulation</strong>.',
                        'Open <strong>Results</strong> and compare the feed and product bars on the composition chart to confirm conversion and separation.',
                    ],
                    'tip' => 'Include trace components that affect phase behaviour even when their flow is negligible — omitting water or a light end is a common cause of a separation that looks better on paper than in the plant.',
                ],
            ],
            'exchanger-design' => [
                'artefact' => 'exchanger design case',
                'inputs'   => 'Hot and cold stream duties, terminal temperatures and allowable pressure drop',
                'outputs'  => 'Surface area, LMTD, effective U and pressure-drop check',
                'verbs'    => [
                    'Shell-and-tube, plate and air-cooled geometry support',
                    'Rating and design modes with LMTD and effectiveness-NTU methods',
                    'Tube-side and shell-side pressure-drop prediction',
                    'Fouling-factor allowance and over-surface reporting',
                    'TEMA-style geometry selection and nozzle sizing checks',
                ],
                'task' => [
                    'title'     => 'Size and rate a heat exchanger',
                    'objective' => 'Establish the required surface area for a duty, then verify the geometry against pressure-drop limits.',
                    'inputs'    => 'Duty, terminal temperatures, allowable pressure drop, fouling factors',
                    'outputs'   => 'Required area, LMTD, effective U and over-surface margin',
                    'steps'     => [
                        'Create the run from the <strong>Simulation</strong> tab and attach it to the project holding the stream data.',
                        'In <strong>Parameters (JSON)</strong> supply the duty and terminal temperatures, for example <code>{"duty_kW":1200,"T_hot_in":180,"T_hot_out":90,"T_cold_in":40,"T_cold_out":110}</code>.',
                        'Add the allowable pressure drop and fouling factors to the same JSON object so the rating pass can flag an undersized geometry.',
                        'Run the case, then open <strong>Configuration</strong> and set the <strong>Default Unit System</strong> to match the datasheet you are working against.',
                        'Read the resulting area and LMTD from the <strong>Results</strong> tab.',
                        'Iterate on the geometry until both the area margin and the pressure drop sit inside specification.',
                    ],
                    'tip' => 'Always rate the geometry you actually intend to buy. A design-mode area is a starting point, not a purchase specification.',
                ],
            ],
            'concurrent-feed' => [
                'artefact' => 'FEED study',
                'inputs'   => 'Equipment list, scope definition and cost basis',
                'outputs'  => 'Capital cost estimate and schedule position',
                'verbs'    => [
                    'Concurrent front-end engineering across process, cost and schedule',
                    'Equipment-list driven capital cost estimation',
                    'Scope-change tracking with revision history',
                    'Order-of-magnitude through definitive estimate classes',
                    'Multi-discipline collaboration on a single project record',
                ],
                'task' => [
                    'title'     => 'Assemble a FEED cost and scope package',
                    'objective' => 'Turn an equipment list into a costed, scheduled front-end package.',
                    'inputs'    => 'Equipment list, unit costs, installation factors',
                    'outputs'   => 'Capital cost estimate with scope basis',
                    'steps'     => [
                        'Create the owning project under <strong>Projects</strong> first, so every FEED artefact rolls up to one record.',
                        'Launch the module and open the <strong>Simulation</strong> tab to create the study.',
                        'Supply the equipment list and cost basis in <strong>Parameters (JSON)</strong>, including installation factors and the estimate class.',
                        'Run the study to produce the costed scope.',
                        'Review the roll-up on the <strong>Results</strong> tab.',
                        'Record the estimate class and basis in the run description so later reviewers know its accuracy band.',
                    ],
                    'tip' => 'State the estimate class (AACE Class 5 through Class 1) in the description. An unlabelled estimate will eventually be quoted as though it were definitive.',
                ],
            ],
            'subsurface-science' => [
                'artefact' => 'reservoir or subsurface model',
                'inputs'   => 'Well logs, seismic horizons and petrophysical properties',
                'outputs'  => 'Volumetrics, production forecast and uncertainty range',
                'verbs'    => [
                    'Reservoir characterisation from log and seismic inputs',
                    'Petrophysical property modelling including porosity and permeability',
                    'Production forecasting with decline-curve and material-balance methods',
                    'Seismic survey management and horizon interpretation',
                    'Uncertainty ranges across low, mid and high cases',
                ],
                'task' => [
                    'title'     => 'Build a reservoir characterisation case',
                    'objective' => 'Combine log and seismic inputs into a property model and produce a volumetric estimate.',
                    'inputs'    => 'Well logs, horizons, petrophysical cut-offs',
                    'outputs'   => 'Volumetrics and a production forecast range',
                    'steps'     => [
                        'Create or select the project that owns the field study.',
                        'Open the module workspace and move to the <strong>Simulation</strong> tab.',
                        'Provide the petrophysical basis in <strong>Parameters (JSON)</strong> — porosity and permeability cut-offs, net-to-gross and contact depths.',
                        'Run the case to generate the property model and volumetrics.',
                        'Inspect the profiles on the <strong>Results</strong> tab.',
                        'Repeat for low, mid and high cases so the forecast carries an explicit uncertainty range.',
                    ],
                    'tip' => 'Record the cut-offs used for every case. Volumetrics quoted without their cut-off basis cannot be compared between studies.',
                ],
            ],
            'energy-optimization' => [
                'artefact' => 'energy targeting study',
                'inputs'   => 'Stream duties, supply and target temperatures, utility costs',
                'outputs'  => 'Energy targets, pinch temperature and utility savings',
                'verbs'    => [
                    'Pinch analysis with composite and grand composite curves',
                    'Minimum utility targeting ahead of network design',
                    'Heat-exchanger network synthesis and retrofit screening',
                    'Utility cost modelling across steam levels and cooling media',
                    'Carbon and energy intensity reporting',
                ],
                'task' => [
                    'title'     => 'Run a pinch analysis and set energy targets',
                    'objective' => 'Establish minimum hot and cold utility demand before committing to a network design.',
                    'inputs'    => 'Stream duties, supply/target temperatures, minimum approach temperature',
                    'outputs'   => 'Pinch temperature and utility targets',
                    'steps'     => [
                        'Create the run on the <strong>Simulation</strong> tab against the project holding the stream data.',
                        'Enter the hot and cold stream table in <strong>Parameters (JSON)</strong>, with supply and target temperatures and duties for each stream.',
                        'Add the minimum approach temperature (<code>dTmin</code>) — it drives the entire target.',
                        'Run the analysis.',
                        'Read the pinch temperature and the minimum utility targets from the <strong>Results</strong> tab.',
                        'Re-run across a range of <code>dTmin</code> values to trade capital against energy before fixing the design.',
                    ],
                    'tip' => 'Targets come first, network second. Designing the network before the target is set is how projects end up paying for exchangers that buy nothing.',
                ],
            ],
            'operations-support' => [
                'artefact' => 'operations monitoring view',
                'inputs'   => 'Live plant tags and operating limits',
                'outputs'  => 'Deviation alerts and shift performance summary',
                'verbs'    => [
                    'Real-time operating window monitoring',
                    'Deviation detection against alarm and constraint limits',
                    'Shift handover reporting',
                    'Plant tag history and trend retrieval',
                    'Operator guidance tied to current plant state',
                ],
                'task' => [
                    'title'     => 'Configure an operating window monitor',
                    'objective' => 'Watch a set of plant tags against their limits and surface deviations as they happen.',
                    'inputs'    => 'Tag list, alarm and constraint limits',
                    'outputs'   => 'Deviation list and trend history',
                    'steps'     => [
                        'Open the module and create the monitoring case on the <strong>Simulation</strong> tab.',
                        'List the plant tags to watch in <strong>Parameters (JSON)</strong>, each with its high and low operating limits.',
                        'Run the case to establish the baseline.',
                        'Use the <strong>Configuration</strong> tab to set the <strong>Output Format</strong> for downstream reporting.',
                        'Review deviations and trends on the <strong>Results</strong> tab.',
                        'Export the summary at shift end for handover.',
                    ],
                    'tip' => 'Set limits from the operating window, not the alarm rack. Alarm limits tell you something already went wrong; operating limits tell you it is about to.',
                ],
            ],
            'advanced-process-control' => [
                'artefact' => 'controller',
                'inputs'   => 'Step-test data, controlled and manipulated variable list',
                'outputs'  => 'Identified model, tuning constants and predicted response',
                'verbs'    => [
                    'Model-predictive control design and commissioning',
                    'Step-test data collection and model identification',
                    'Controller tuning with constraint handling',
                    'Closed-loop performance monitoring and service factor tracking',
                    'Inferential property estimation from measured variables',
                ],
                'task' => [
                    'title'     => 'Identify a model and tune a controller',
                    'objective' => 'Turn step-test data into an identified process model and a tuned controller.',
                    'inputs'    => 'Step-test data, CV/MV pairing, constraint limits',
                    'outputs'   => 'Identified model, tuning constants, predicted closed-loop response',
                    'steps'     => [
                        'Create the controller case on the <strong>Simulation</strong> tab and attach it to the unit project.',
                        'Supply the controlled and manipulated variable list plus the step-test data reference in <strong>Parameters (JSON)</strong>.',
                        'Set the constraint limits for every manipulated variable so the tuning respects them.',
                        'Run the identification.',
                        'Open <strong>Configuration</strong> and tighten <strong>Convergence Tolerance</strong> if the identified model is noisy.',
                        'Review the predicted closed-loop response on the <strong>Results</strong> tab before commissioning.',
                    ],
                    'tip' => 'Validate the identified model against a step test it has not seen. A model that only fits its own training data will not survive first commissioning.',
                ],
            ],
            'dynamic-optimization' => [
                'artefact' => 'dynamic optimisation case',
                'inputs'   => 'Objective function, decision variables and constraint set',
                'outputs'  => 'Optimal trajectory and converged objective value',
                'verbs'    => [
                    'Dynamic and steady-state optimisation with nonlinear constraints',
                    'Objective-function definition across economics and throughput',
                    'Trajectory optimisation for transitions and start-up',
                    'Sensitivity analysis on active constraints',
                    'Convergence diagnostics with iteration history',
                ],
                'task' => [
                    'title'     => 'Solve a constrained optimisation case',
                    'objective' => 'Define an objective and constraint set, then solve for the optimal operating trajectory.',
                    'inputs'    => 'Objective function, decision variables, constraint bounds',
                    'outputs'   => 'Optimal trajectory and active-constraint set',
                    'steps'     => [
                        'Create the optimisation run on the <strong>Simulation</strong> tab.',
                        'Define the objective, decision variables and their bounds in <strong>Parameters (JSON)</strong>.',
                        'State every constraint explicitly — an unbounded decision variable will drive the solver to a meaningless optimum.',
                        'On the <strong>Configuration</strong> tab raise <strong>Max Iterations</strong> for a large decision space.',
                        'Run the case and watch the convergence history.',
                        'Inspect which constraints are active at the solution on the <strong>Results</strong> tab.',
                    ],
                    'tip' => 'Check which constraints are active at the optimum. If none are, the objective or the bounds are almost certainly mis-specified.',
                ],
            ],
            'mes' => [
                'artefact' => 'production record',
                'inputs'   => 'Batch or campaign definition, equipment and material genealogy',
                'outputs'  => 'Production record, yield and OEE reporting',
                'verbs'    => [
                    'Batch and campaign execution tracking',
                    'Material genealogy and full lot traceability',
                    'Electronic batch records suitable for regulated manufacture',
                    'OEE, yield and downtime reporting',
                    'Equipment status and utilisation monitoring',
                ],
                'task' => [
                    'title'     => 'Record and close out a production batch',
                    'objective' => 'Track a batch from release to close-out with full material genealogy.',
                    'inputs'    => 'Batch definition, equipment assignment, material lots',
                    'outputs'   => 'Completed production record with yield and genealogy',
                    'steps'     => [
                        'Create the batch record on the <strong>Simulation</strong> tab against the production project.',
                        'Enter the batch definition in <strong>Parameters (JSON)</strong> — product code, target quantity, equipment train and material lots.',
                        'Run the record to open the batch.',
                        'Review yield and equipment utilisation on the <strong>Results</strong> tab.',
                        'Set <strong>Output Format</strong> on the <strong>Configuration</strong> tab to the format your quality system ingests.',
                        'Export the completed record for review and archival.',
                    ],
                    'tip' => 'Capture material lots at the point of charging, not at close-out. Genealogy reconstructed after the fact is the first thing an auditor will challenge.',
                ],
            ],
            'petroleum-supply-chain' => [
                'artefact' => 'supply chain plan',
                'inputs'   => 'Crude assays, refinery yields, demand forecast and logistics costs',
                'outputs'  => 'Optimised crude slate, blend recipe and distribution plan',
                'verbs'    => [
                    'Crude assay management and blend optimisation',
                    'Refinery yield modelling against a demand forecast',
                    'Distribution and logistics cost optimisation',
                    'Blend recipe generation against product specifications',
                    'Margin analysis across alternative crude slates',
                ],
                'task' => [
                    'title'     => 'Optimise a crude slate and blend recipe',
                    'objective' => 'Select the crude slate and blend that meets product specification at the best margin.',
                    'inputs'    => 'Crude assays, product specifications, demand forecast, prices',
                    'outputs'   => 'Optimised slate, blend recipe and margin',
                    'steps'     => [
                        'Create the planning run on the <strong>Simulation</strong> tab.',
                        'Reference the crude assays and product specifications in <strong>Parameters (JSON)</strong>, with the demand forecast for the period.',
                        'Add crude and product prices so the objective reflects margin rather than volume alone.',
                        'Run the optimisation.',
                        'Read the recommended slate and blend recipe from the <strong>Results</strong> tab.',
                        'Re-run against alternative price scenarios before committing to a cargo.',
                    ],
                    'tip' => 'Re-run the plan whenever the assay changes. A blend recipe tuned to last quarter\'s assay will quietly drift off specification.',
                ],
            ],
            'supply-chain-mgmt' => [
                'artefact' => 'production schedule',
                'inputs'   => 'Demand forecast, capacity and inventory position',
                'outputs'  => 'Feasible schedule with inventory projection',
                'verbs'    => [
                    'Demand forecasting and consensus planning',
                    'Finite-capacity production scheduling',
                    'Inventory optimisation across the network',
                    'Order promising against available capacity',
                    'Scenario comparison for demand and capacity changes',
                ],
                'task' => [
                    'title'     => 'Generate a feasible production schedule',
                    'objective' => 'Turn a demand forecast into a schedule that respects capacity and inventory limits.',
                    'inputs'    => 'Demand forecast, capacity, opening inventory',
                    'outputs'   => 'Feasible schedule with projected inventory',
                    'steps'     => [
                        'Create the scheduling run on the <strong>Simulation</strong> tab.',
                        'Enter the demand forecast, unit capacities and opening inventory in <strong>Parameters (JSON)</strong>.',
                        'Include changeover times — a schedule that ignores them will not survive the plant floor.',
                        'Run the schedule.',
                        'Check the projected inventory profile on the <strong>Results</strong> tab for stock-outs and build-ups.',
                        'Adjust and re-run until the schedule is feasible across the whole horizon.',
                    ],
                    'tip' => 'A schedule with no slack is not an efficient schedule, it is a fragile one. Leave room for the plant to recover from a single upset.',
                ],
            ],
            'apm' => [
                'artefact' => 'asset health case',
                'inputs'   => 'Asset register, condition data and failure history',
                'outputs'  => 'Health score, criticality ranking and maintenance recommendation',
                'verbs'    => [
                    'Asset health scoring from condition and process data',
                    'Failure-mode and criticality analysis',
                    'Predictive maintenance scheduling ahead of failure',
                    'Reliability metrics including MTBF and availability',
                    'Maintenance event history and cost tracking',
                ],
                'task' => [
                    'title'     => 'Score asset health and rank criticality',
                    'objective' => 'Combine condition data and failure history into a health score and a ranked maintenance list.',
                    'inputs'    => 'Asset register, condition readings, failure history',
                    'outputs'   => 'Health scores, criticality ranking, recommended actions',
                    'steps'     => [
                        'Create the assessment run on the <strong>Simulation</strong> tab against the asset project.',
                        'Reference the asset register and condition data in <strong>Parameters (JSON)</strong>.',
                        'Include failure history so the criticality ranking reflects real consequence, not just age.',
                        'Run the assessment.',
                        'Review the health scores and ranking on the <strong>Results</strong> tab.',
                        'Export the ranked list into the maintenance planning cycle.',
                    ],
                    'tip' => 'Rank by consequence of failure, not by probability alone. A frequently failing pump with a spare matters less than a single unspared compressor.',
                ],
            ],
            'industrial-data-fabric' => [
                'artefact' => 'data pipeline',
                'inputs'   => 'Source system connection, tag mapping and transformation rules',
                'outputs'  => 'Contextualised, quality-checked data set',
                'verbs'    => [
                    'Source system connectivity across historians, MES and ERP',
                    'Tag mapping and asset-model contextualisation',
                    'Transformation and data-quality rule enforcement',
                    'Unified access layer across heterogeneous sources',
                    'Lineage tracking from source tag to consumed value',
                ],
                'task' => [
                    'title'     => 'Build a contextualised data pipeline',
                    'objective' => 'Connect a source system, map its tags to the asset model and enforce quality rules.',
                    'inputs'    => 'Source connection, tag list, transformation and quality rules',
                    'outputs'   => 'Contextualised data set with lineage',
                    'steps'     => [
                        'Create the pipeline case on the <strong>Simulation</strong> tab.',
                        'Define the source connection and tag mapping in <strong>Parameters (JSON)</strong>.',
                        'Add transformation and data-quality rules — unit conversion, range checks and dead-band filters.',
                        'Run the pipeline to process the mapped data.',
                        'Confirm coverage and rule violations on the <strong>Results</strong> tab.',
                        'Set the <strong>Output Format</strong> to match the consuming application.',
                    ],
                    'tip' => 'Map tags to the asset model, not to a flat name list. Contextualised data survives a plant reorganisation; a flat tag list does not.',
                ],
            ],
            'digital-grid-mgmt' => [
                'artefact' => 'grid network model',
                'inputs'   => 'Network topology, load profile and generation mix',
                'outputs'  => 'Power flow solution and stability assessment',
                'verbs'    => [
                    'Power flow and contingency analysis across the network',
                    'SCADA point integration and real-time grid state monitoring',
                    'Load forecasting and demand-response evaluation',
                    'Renewable generation integration and curtailment analysis',
                    'Outage management and restoration sequencing',
                ],
                'task' => [
                    'title'     => 'Solve a network power flow case',
                    'objective' => 'Model the network topology and load, then solve for power flow and check stability margins.',
                    'inputs'    => 'Network topology, load profile, generation dispatch',
                    'outputs'   => 'Power flow solution, voltage profile, contingency results',
                    'steps'     => [
                        'Create the network case on the <strong>Simulation</strong> tab.',
                        'Describe the topology, load profile and generation dispatch in <strong>Parameters (JSON)</strong>.',
                        'Run the base-case power flow first and confirm it converges before adding contingencies.',
                        'Raise <strong>Max Iterations</strong> on the <strong>Configuration</strong> tab for a large or weakly meshed network.',
                        'Review the voltage profile and loading on the <strong>Results</strong> tab.',
                        'Re-run under contingency to confirm the network holds with a element out of service.',
                    ],
                    'tip' => 'Always establish a converged base case before running contingencies. A contingency that fails to solve on top of an unconverged base tells you nothing.',
                ],
            ],
        ];
    }

    /** Profile for a category slug, falling back to a generic engineering profile. */
    public static function profile($categorySlug)
    {
        $p = self::profiles();
        if (isset($p[$categorySlug])) {
            return $p[$categorySlug];
        }
        return [
            'artefact' => 'engineering case',
            'inputs'   => 'Process data and operating conditions',
            'outputs'  => 'Calculated results and performance metrics',
            'verbs'    => [
                'Engineering calculation engine with configurable convergence',
                'Project-scoped case management and revision history',
                'Result visualisation with charts and data tables',
                'Export to CSV, JSON and XML for downstream use',
                'Role-based access aligned to the platform licence model',
            ],
            'task' => [
                'title'     => 'Create and solve an engineering case',
                'objective' => 'Set up the case against a project and solve it.',
                'inputs'    => 'Operating conditions and case parameters',
                'outputs'   => 'Solved case with result charts',
                'steps'     => [
                    'Open the <strong>Simulation</strong> tab and name the run.',
                    'Select the project the case belongs to.',
                    'Supply the case parameters in <strong>Parameters (JSON)</strong>.',
                    'Press <strong>Create &amp; Run Simulation</strong>.',
                    'Review the output on the <strong>Results</strong> tab.',
                    'Export the results in the format set on the <strong>Configuration</strong> tab.',
                ],
                'tip' => 'Keep one project per study. Runs scattered across projects are difficult to compare later.',
            ],
        ];
    }

    /**
     * Feature bullets for the brochure: module-specific lead, then category
     * capabilities, then the platform-level guarantee.
     * @return string[]
     */
    public static function features($module, $categorySlug)
    {
        $prof = self::profile($categorySlug);
        $desc = trim((string)($module['description'] ?? ''));

        $out = [];
        if ($desc !== '') {
            $out[] = rtrim($desc, '.') . '.';
        }
        foreach (array_slice($prof['verbs'], 0, 4) as $v) {
            $out[] = $v;
        }
        $tier = $module['license_required'] ?? 'standard';
        $out[] = 'Delivered under the ' . ucfirst($tier) . ' licence tier with full audit logging.';
        return $out;
    }

    /**
     * Task walkthroughs for the How-To Manual. Every module gets the category's
     * signature task plus the four universal platform workflows, all phrased
     * against the real module workspace.
     * @return array[] each ['title','objective','inputs','outputs','steps'=>[],'tip']
     */
    public static function tasks($module, $categorySlug, $categoryName)
    {
        $prof = self::profile($categorySlug);
        $name = $module['name'] ?? 'the module';
        $tier = ucfirst($module['license_required'] ?? 'standard');

        $tasks = [];

        // 1 — Reach the module and understand its scope.
        $tasks[] = [
            'title'     => 'Open ' . $name . ' and review its scope',
            'objective' => 'Locate the module, confirm your licence covers it and read its capability summary before starting work.',
            'inputs'    => 'A signed-in account with the ' . $tier . ' licence tier',
            'outputs'   => 'The module workspace, open on the Overview tab',
            'steps'     => [
                'From the sidebar choose <strong>Modules</strong>, then the <strong>' . htmlspecialchars($categoryName) . '</strong> category.',
                'Select <strong>' . htmlspecialchars($name) . '</strong> from the category listing to open its workspace.',
                'Read the <strong>Overview</strong> tab for the capability summary and the getting-started sequence.',
                'Check the <strong>Version Info</strong> panel on the right — it shows the module version, category, licence state and status.',
                'If the workspace reports that a licence is required, request one from the Licensing Portal before continuing.',
            ],
            'tip' => 'The licence badge in the workspace header tells you immediately whether the module will run or stop at the compute step.',
        ];

        // 2 — The category's signature engineering task.
        $sig = $prof['task'];
        $tasks[] = [
            'title'     => $sig['title'],
            'objective' => $sig['objective'],
            'inputs'    => $sig['inputs'],
            'outputs'   => $sig['outputs'],
            'steps'     => $sig['steps'],
            'tip'       => $sig['tip'],
        ];

        // 3 — Solver and unit configuration.
        $tasks[] = [
            'title'     => 'Configure solver behaviour and units',
            'objective' => 'Set the unit system, convergence criteria and export format the module will use.',
            'inputs'    => 'Required unit system, tolerance and iteration budget',
            'outputs'   => 'A saved module configuration',
            'steps'     => [
                'Open the <strong>Configuration</strong> tab in the module workspace.',
                'Set <strong>Default Unit System</strong> to SI, Imperial or CGS to match the data you are working from.',
                'Set <strong>Convergence Tolerance</strong> — the default of <code>0.0001</code> suits most cases; tighten it only when the result is sensitive.',
                'Set <strong>Max Iterations</strong> to give the solver enough budget for the size of the case.',
                'Choose the <strong>Output Format</strong> (JSON, CSV or XML) for exported results.',
                'Leave <strong>Auto-save simulation state</strong> enabled so a long run is not lost, then press <strong>Save Configuration</strong>.',
            ],
            'tip' => 'Change one setting at a time. Loosening the tolerance and raising the iteration count together makes it impossible to tell which one fixed the convergence.',
        ];

        // 4 — Run and monitor.
        $tasks[] = [
            'title'     => 'Run the case and track its status',
            'objective' => 'Execute the run and follow it from draft through to completed.',
            'inputs'    => 'A configured simulation attached to a project',
            'outputs'   => 'A completed run with stored results',
            'steps'     => [
                'From the <strong>Simulation</strong> tab press <strong>Create &amp; Run Simulation</strong>.',
                'The run is created against the selected project and appears under <strong>Simulations</strong>.',
                'Open <strong>Simulations</strong> from the sidebar to see every run with its current status.',
                'A run in <em>draft</em> shows a <strong>Run Simulation</strong> button on its detail page; use it to execute the case.',
                'Wait for the status to reach <em>completed</em> — the platform advances it from draft through running automatically.',
                'Use the <strong>Results</strong> action against the completed run to open its output.',
            ],
            'tip' => 'Give every run a name that identifies the case, not just the module. "Depropanizer 98% recovery" is searchable six months later; "Run 1" is not.',
        ];

        // 5 — Interpret and export.
        $tasks[] = [
            'title'     => 'Review results and export them',
            'objective' => 'Interpret the charts produced by the run and export the data for reporting.',
            'inputs'    => 'A completed simulation run',
            'outputs'   => $prof['outputs'],
            'steps'     => [
                'Open the <strong>Results</strong> tab in the workspace, or the results view for the run under <strong>Simulations</strong>.',
                'Read the <strong>Temperature Profile</strong> chart for the thermal behaviour across the case.',
                'Read the <strong>Pressure Drop</strong> chart to confirm hydraulics stay within specification.',
                'Compare the feed and product series on the <strong>Composition Analysis</strong> chart.',
                'Cross-check the headline numbers against an independent hand calculation before circulating them.',
                'Export using the format set on the <strong>Configuration</strong> tab and attach the export to the project record.',
            ],
            'tip' => 'Never issue a result you have not sanity-checked against a hand calculation or a known case. A converged answer is not automatically a correct one.',
        ];

        return $tasks;
    }
}
