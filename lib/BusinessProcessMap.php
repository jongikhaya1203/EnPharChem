<?php
/**
 * EnPharChem - Business Process Map content
 * -----------------------------------------
 * Maps the platform's business processes to the use cases they serve, the job
 * titles (and platform roles) that carry them out, and a worked example for
 * each. Editorial content — grounded in the real routes, modules and the
 * users.role ENUM (superuser, admin, engineer, operator, viewer) — consumed by
 * BusinessProcessPdfBuilder and the HTML edition.
 */

class BusinessProcessMap
{
    /** Platform roles, as defined by the users.role column, with what each conveys. */
    public static function roleLegend()
    {
        return [
            ['role' => 'superuser',  'scope' => 'Unrestricted platform owner: all administration plus licence issuance and portal governance.'],
            ['role' => 'admin',      'scope' => 'Administers users, modules, settings, content, sample data and module licences.'],
            ['role' => 'engineer',   'scope' => 'Builds and runs engineering work: projects, simulations and the discipline modules.'],
            ['role' => 'operator',   'scope' => 'Runs and monitors day-to-day operations: batches, schedules, grid and asset tasks.'],
            ['role' => 'viewer',     'scope' => 'Read-only access to review results, dashboards and reports.'],
        ];
    }

    /**
     * The business processes. Each:
     *   name, summary
     *   appArea   - where it lives in the platform
     *   useCases  - string[]
     *   actors    - [['title'=>job title, 'role'=>platform role], ...]
     *   example   - ['title','steps'=>[],'outcome']
     */
    public static function processes()
    {
        return [
            [
                'name'    => 'Process Simulation & Design',
                'summary' => 'Model steady-state processes, close heat and material balances, and compare design cases before committing to a plant configuration.',
                'appArea' => 'Modules > Process Simulation (Energy / Chemicals), Flowsheet Simulator; Projects; Simulations',
                'useCases' => [
                    'Build a steady-state flowsheet and converge it to a heat and material balance.',
                    'Run rigorous vapour-liquid equilibrium (Peng-Robinson) for a two-phase flash.',
                    'Size a distillation column by light/heavy key recovery.',
                    'Sweep feed conditions and operating set points across design cases.',
                ],
                'actors' => [
                    ['title' => 'Process Engineer',       'role' => 'engineer'],
                    ['title' => 'Simulation Specialist',  'role' => 'engineer'],
                    ['title' => 'Lead Process Engineer',  'role' => 'admin'],
                    ['title' => 'Design Reviewer',        'role' => 'viewer'],
                ],
                'example' => [
                    'title' => 'A Process Engineer designs a depropanizer',
                    'steps' => [
                        'Create the project "NGL Fractionation" under Projects.',
                        'Open the Flowsheet Simulator and place a feed, a shortcut column and two products.',
                        'Set the light key to Propane and the heavy key to i-Butane with 98% recoveries.',
                        'Select the Peng-Robinson property package and run the flowsheet.',
                        'Read the distillate purity and reboiler duty from the Results tab.',
                    ],
                    'outcome' => 'A converged column design with product specifications and duties, saved against the project for review.',
                ],
            ],
            [
                'name'    => 'Heat Integration & Energy Optimization',
                'summary' => 'Target minimum utility demand and design the exchanger network that achieves it, before spending capital on equipment.',
                'appArea' => 'Modules > Energy & Utilities Optimization; Exchanger Design & Rating',
                'useCases' => [
                    'Run a pinch analysis to establish minimum hot and cold utility targets.',
                    'Synthesise or retrofit a heat-exchanger network against the target.',
                    'Rate a shell-and-tube exchanger against duty and pressure-drop limits.',
                ],
                'actors' => [
                    ['title' => 'Energy Engineer',    'role' => 'engineer'],
                    ['title' => 'Utilities Engineer', 'role' => 'engineer'],
                    ['title' => 'Plant Manager',      'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'An Energy Engineer cuts steam consumption',
                    'steps' => [
                        'Create the run against the utilities project and enter the hot/cold stream table.',
                        'Set the minimum approach temperature and run the pinch analysis.',
                        'Read the pinch temperature and the minimum utility targets from Results.',
                        'Re-run across a range of approach temperatures to trade capital against energy.',
                    ],
                    'outcome' => 'A defensible utility target and the approach temperature that balances capital against operating cost.',
                ],
            ],
            [
                'name'    => 'Front-End Engineering & Cost Estimation',
                'summary' => 'Turn an equipment list into a costed, scheduled front-end package with an explicit accuracy class.',
                'appArea' => 'Modules > Concurrent FEED; Projects; Cost Estimates',
                'useCases' => [
                    'Cost a project from its equipment list and installation factors.',
                    'Track scope changes against a baseline with revision history.',
                    'Produce estimates from order-of-magnitude through to definitive class.',
                ],
                'actors' => [
                    ['title' => 'FEED Engineer',    'role' => 'engineer'],
                    ['title' => 'Cost Estimator',   'role' => 'engineer'],
                    ['title' => 'Project Engineer', 'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'A Cost Estimator prepares a Class 3 estimate',
                    'steps' => [
                        'Create the owning project so every FEED artefact rolls up to one record.',
                        'Enter the equipment list, unit costs and installation factors as case parameters.',
                        'Run the study to produce the costed scope.',
                        'Record the estimate class and basis in the run description.',
                    ],
                    'outcome' => 'A capital cost estimate with a stated accuracy class and a traceable basis.',
                ],
            ],
            [
                'name'    => 'Advanced Process Control & Optimization',
                'summary' => 'Identify process models from plant data, tune controllers within constraints, and optimise operating trajectories.',
                'appArea' => 'Modules > Advanced Process Control; Dynamic Optimization',
                'useCases' => [
                    'Collect step-test data and identify a process model.',
                    'Tune a model-predictive controller with constraint handling.',
                    'Solve a constrained optimisation for the best operating trajectory.',
                ],
                'actors' => [
                    ['title' => 'Control Engineer', 'role' => 'engineer'],
                    ['title' => 'APC Engineer',     'role' => 'engineer'],
                    ['title' => 'Panel Operator',   'role' => 'operator'],
                ],
                'example' => [
                    'title' => 'A Control Engineer commissions an MPC',
                    'steps' => [
                        'Create the controller case and reference the step-test data and CV/MV list.',
                        'Set the constraint limits for every manipulated variable.',
                        'Run the identification and review the predicted closed-loop response.',
                        'Validate the model against a step test it has not seen before commissioning.',
                    ],
                    'outcome' => 'An identified model and tuning that respects constraints and holds up on unseen data.',
                ],
            ],
            [
                'name'    => 'Manufacturing Execution',
                'summary' => 'Execute and record production batches with full material genealogy and shift-level performance reporting.',
                'appArea' => 'Modules > Manufacturing Execution Systems; Simulations (batch records)',
                'useCases' => [
                    'Release, execute and close a production batch.',
                    'Maintain material genealogy and full lot traceability.',
                    'Report yield, OEE and downtime by shift.',
                ],
                'actors' => [
                    ['title' => 'Production Manager', 'role' => 'admin'],
                    ['title' => 'Batch Coordinator',  'role' => 'engineer'],
                    ['title' => 'Shift Operator',     'role' => 'operator'],
                ],
                'example' => [
                    'title' => 'A Shift Operator records a batch',
                    'steps' => [
                        'Create the batch record against the production project.',
                        'Enter the product code, target quantity, equipment train and material lots at charging.',
                        'Run the record to open the batch and follow it to completion.',
                        'Review yield and utilisation, then export the completed record for quality review.',
                    ],
                    'outcome' => 'A closed batch record with genealogy captured at source, ready for review and archival.',
                ],
            ],
            [
                'name'    => 'Asset Performance Management',
                'summary' => 'Turn condition and failure data into asset health scores, criticality rankings and maintenance recommendations.',
                'appArea' => 'Modules > Asset Performance Management',
                'useCases' => [
                    'Score asset health from condition and process data.',
                    'Rank criticality by consequence of failure, not age alone.',
                    'Schedule predictive maintenance ahead of failure.',
                ],
                'actors' => [
                    ['title' => 'Reliability Engineer', 'role' => 'engineer'],
                    ['title' => 'Maintenance Planner',  'role' => 'operator'],
                    ['title' => 'Asset Manager',        'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'A Reliability Engineer ranks a rotating-equipment fleet',
                    'steps' => [
                        'Create the assessment run against the asset project.',
                        'Reference the asset register, condition readings and failure history.',
                        'Run the assessment to produce health scores and a criticality ranking.',
                        'Export the ranked list into the maintenance planning cycle.',
                    ],
                    'outcome' => 'A consequence-weighted maintenance priority list the planner can act on.',
                ],
            ],
            [
                'name'    => 'Supply Chain & Blend Planning',
                'summary' => 'Optimise the crude slate and blend recipe, and generate feasible production schedules against demand.',
                'appArea' => 'Modules > Petroleum Supply Chain; Supply Chain Management',
                'useCases' => [
                    'Select the crude slate and blend that meets specification at best margin.',
                    'Generate a feasible, finite-capacity production schedule.',
                    'Optimise inventory and distribution across the network.',
                ],
                'actors' => [
                    ['title' => 'Supply Chain Planner', 'role' => 'engineer'],
                    ['title' => 'Blend Optimiser',      'role' => 'engineer'],
                    ['title' => 'Production Scheduler', 'role' => 'operator'],
                ],
                'example' => [
                    'title' => 'A Supply Chain Planner picks next month\'s crude slate',
                    'steps' => [
                        'Create the planning run and reference the crude assays and product specifications.',
                        'Add the demand forecast and crude/product prices so the objective reflects margin.',
                        'Run the optimisation and read the recommended slate and blend recipe.',
                        'Re-run against alternative price scenarios before committing to a cargo.',
                    ],
                    'outcome' => 'A margin-optimal crude slate and blend recipe with scenario sensitivity.',
                ],
            ],
            [
                'name'    => 'Subsurface & Reservoir Management',
                'summary' => 'Combine log and seismic inputs into reservoir property models, volumetrics and production forecasts with an uncertainty range.',
                'appArea' => 'Modules > Subsurface Science & Engineering',
                'useCases' => [
                    'Characterise a reservoir from log and seismic inputs.',
                    'Estimate volumetrics against explicit petrophysical cut-offs.',
                    'Forecast production across low, mid and high cases.',
                ],
                'actors' => [
                    ['title' => 'Reservoir Engineer', 'role' => 'engineer'],
                    ['title' => 'Geoscientist',       'role' => 'engineer'],
                    ['title' => 'Subsurface Manager', 'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'A Reservoir Engineer estimates volumetrics',
                    'steps' => [
                        'Create or select the field study project.',
                        'Provide the petrophysical basis: porosity and permeability cut-offs, net-to-gross, contacts.',
                        'Run the case to generate the property model and volumetrics.',
                        'Repeat for low, mid and high cases to carry an explicit uncertainty range.',
                    ],
                    'outcome' => 'A volumetric estimate with a documented cut-off basis and an uncertainty range.',
                ],
            ],
            [
                'name'    => 'Digital Grid Management',
                'summary' => 'Model network topology and load, solve power flow, and confirm the grid holds under contingency.',
                'appArea' => 'Modules > Digital Grid Management',
                'useCases' => [
                    'Solve a base-case power flow across the network.',
                    'Run contingency analysis for elements out of service.',
                    'Forecast load and evaluate demand response.',
                ],
                'actors' => [
                    ['title' => 'Grid Operator',   'role' => 'operator'],
                    ['title' => 'Network Planner', 'role' => 'engineer'],
                    ['title' => 'Grid Manager',    'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'A Grid Operator validates a network under contingency',
                    'steps' => [
                        'Create the network case and describe the topology, load profile and dispatch.',
                        'Run the base-case power flow and confirm it converges.',
                        'Review the voltage profile and line loading from Results.',
                        'Re-run under contingency to confirm the network holds with an element out.',
                    ],
                    'outcome' => 'A converged base case plus contingency results confirming secure operation.',
                ],
            ],
            [
                'name'    => 'Industrial Data Fabric',
                'summary' => 'Connect source systems, contextualise tags against the asset model, and enforce data-quality rules.',
                'appArea' => 'Modules > Industrial Data Fabric',
                'useCases' => [
                    'Connect historians, MES and ERP into a unified access layer.',
                    'Map tags to the asset model for durable context.',
                    'Enforce transformation and data-quality rules with lineage.',
                ],
                'actors' => [
                    ['title' => 'Data Engineer',           'role' => 'engineer'],
                    ['title' => 'Integration Specialist',  'role' => 'engineer'],
                    ['title' => 'Data Platform Owner',     'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'A Data Engineer builds a contextualised pipeline',
                    'steps' => [
                        'Create the pipeline case and define the source connection and tag mapping.',
                        'Add transformation and quality rules: unit conversion, range checks, dead-band filters.',
                        'Run the pipeline and confirm coverage and rule violations from Results.',
                        'Set the output format to match the consuming application.',
                    ],
                    'outcome' => 'A quality-checked, asset-model-contextualised data set with lineage from source tag to value.',
                ],
            ],
            [
                'name'    => 'Training, Assessment & Certification',
                'summary' => 'Build competency through structured courses, formal assessments and issued certificates.',
                'appArea' => 'Training > Courses, Assessments, Certificates; Control Panel > Training Material',
                'useCases' => [
                    'Enrol in and complete a course with lessons and labs.',
                    'Sit a formal assessment and receive a graded result.',
                    'Earn and retrieve a certificate on passing.',
                ],
                'actors' => [
                    ['title' => 'Training Lead',       'role' => 'admin'],
                    ['title' => 'Trainee / Engineer',  'role' => 'engineer'],
                    ['title' => 'Operator (Trainee)',  'role' => 'operator'],
                    ['title' => 'Assessor',            'role' => 'admin'],
                ],
                'example' => [
                    'title' => 'An Engineer earns a module certificate',
                    'steps' => [
                        'Open Training and select the relevant course, then work through its lessons.',
                        'Start the assessment from the course detail page.',
                        'Submit answers and view the graded result.',
                        'On passing, open My Certificates to retrieve the certificate.',
                    ],
                    'outcome' => 'A recorded pass and an issued certificate against the engineer\'s account.',
                ],
            ],
            [
                'name'    => 'Licensing & Entitlement Management',
                'summary' => 'Govern who may run which modules, through the online portal or an air-gapped signed licence file.',
                'appArea' => 'Control Panel > Licensing Portal; Module License Manager',
                'useCases' => [
                    'Request access to a module from within the tool.',
                    'Issue, grant or revoke a licence per user and module.',
                    'Approve pending licence requests and audit grants.',
                    'Entitle an air-gapped install with a vendor-signed licence file.',
                ],
                'actors' => [
                    ['title' => 'License Manager',       'role' => 'superuser'],
                    ['title' => 'System Administrator',  'role' => 'admin'],
                    ['title' => 'Requesting Engineer',   'role' => 'engineer'],
                ],
                'example' => [
                    'title' => 'An Engineer requests, and an Admin grants, Flowsheet access',
                    'steps' => [
                        'The engineer opens the Flowsheet Simulator and submits a licence request from the tool.',
                        'The administrator opens the Licensing Portal and reviews pending requests.',
                        'The administrator issues a licence and grants the Flowsheet Simulator module to the engineer.',
                        'The engineer re-runs the simulator; compute is now permitted.',
                    ],
                    'outcome' => 'A governed, audited grant that unlocks the module for exactly the entitled user.',
                ],
            ],
            [
                'name'    => 'Platform Administration & Governance',
                'summary' => 'Administer users and roles, manage modules and settings, and govern content, directory and sample data.',
                'appArea' => 'Admin > Users, Modules, Settings; Control Panel > Active Directory, CMS, Data Management',
                'useCases' => [
                    'Onboard users and assign platform roles.',
                    'Activate or configure modules and platform settings.',
                    'Manage CMS pages, directory groups and sample data.',
                ],
                'actors' => [
                    ['title' => 'System Administrator', 'role' => 'admin'],
                    ['title' => 'Super Administrator',  'role' => 'superuser'],
                ],
                'example' => [
                    'title' => 'A System Administrator onboards a new engineer',
                    'steps' => [
                        'Open Admin > Users and review the existing accounts.',
                        'Create the account and assign the engineer role.',
                        'Grant the module licences the engineer needs from the Licensing Portal.',
                        'Confirm access, then hand over credentials for a first-login password change.',
                    ],
                    'outcome' => 'A correctly-scoped account that can reach exactly the work it is entitled to.',
                ],
            ],
        ];
    }
}
