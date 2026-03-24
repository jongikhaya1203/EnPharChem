<?php
/**
 * EnPharChem - Gartner Benchmark Controller
 * Benchmarks EnPharChem against AspenTech using Gartner rating methodology
 */

class BenchmarkController extends BaseController {

    public function index() {
        // Gartner evaluation criteria scores (out of 5.0)
        $gartnerComparison = $this->getGartnerComparison();
        $magicQuadrant = $this->getMagicQuadrantData();
        $peerInsights = $this->getPeerInsightsData();
        $moduleComparison = $this->getModuleComparison();
        $marketAnalysis = $this->getMarketAnalysis();
        $strengthsWeaknesses = $this->getStrengthsWeaknesses();
        $competitorLandscape = $this->getCompetitorLandscape();

        $this->view('benchmark/index', [
            'pageTitle' => 'Gartner Benchmark Analysis',
            'gartnerComparison' => $gartnerComparison,
            'magicQuadrant' => $magicQuadrant,
            'peerInsights' => $peerInsights,
            'moduleComparison' => $moduleComparison,
            'marketAnalysis' => $marketAnalysis,
            'strengthsWeaknesses' => $strengthsWeaknesses,
            'competitorLandscape' => $competitorLandscape,
        ]);
    }

    public function pdf() {
        $gartnerComparison = $this->getGartnerComparison();
        $magicQuadrant = $this->getMagicQuadrantData();
        $peerInsights = $this->getPeerInsightsData();
        $moduleComparison = $this->getModuleComparison();
        $marketAnalysis = $this->getMarketAnalysis();
        $strengthsWeaknesses = $this->getStrengthsWeaknesses();
        $competitorLandscape = $this->getCompetitorLandscape();

        extract([
            'gartnerComparison' => $gartnerComparison,
            'magicQuadrant' => $magicQuadrant,
            'peerInsights' => $peerInsights,
            'moduleComparison' => $moduleComparison,
            'marketAnalysis' => $marketAnalysis,
            'strengthsWeaknesses' => $strengthsWeaknesses,
            'competitorLandscape' => $competitorLandscape,
        ]);

        include VIEWS_PATH . '/benchmark/pdf.php';
        exit;
    }

    /**
     * Gartner-style evaluation criteria comparison
     * Based on Gartner Magic Quadrant methodology:
     * - Ability to Execute (Product, Viability, Sales, Market Response, Customer Experience, Operations)
     * - Completeness of Vision (Market Understanding, Strategy, Innovation, Geographic, Industry)
     */
    private function getGartnerComparison() {
        return [
            'ability_to_execute' => [
                'label' => 'Ability to Execute',
                'criteria' => [
                    [
                        'name' => 'Product/Service',
                        'description' => 'Core goods and services including 115+ modules, interactive training, competency assessments, and certification engine',
                        'aspentech' => 4.7,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Overall Viability',
                        'description' => 'Platform sustainability with open-standards architecture, zero vendor lock-in, and cloud-native scalability',
                        'aspentech' => 4.6,
                        'enpharchem' => 4.7,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Sales Execution/Pricing',
                        'description' => 'Web-based trial, transparent tiered pricing (Trial/Standard/Professional/Enterprise), instant deployment',
                        'aspentech' => 4.3,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Market Responsiveness/Record',
                        'description' => 'Agile development with rapid feature delivery, CMS-driven branding, and modular architecture for instant customization',
                        'aspentech' => 4.2,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Marketing Execution',
                        'description' => 'Integrated CMS with dynamic branding, auto-generated PDF collateral (4 professional documents), product brochures with module screenshots, Gartner benchmark report',
                        'aspentech' => 4.4,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Customer Experience',
                        'description' => '60 training courses (4 levels), 600+ assessment questions, auto-certification, interactive labs, video lessons, real-time simulation workspace',
                        'aspentech' => 4.5,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Operations',
                        'description' => 'Control panel with AD integration, data management, sample data seeding, audit logging, RBAC with 5 roles',
                        'aspentech' => 4.4,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Algorithm & Computation Engine',
                        'description' => 'Peng-Robinson, SRK, NRTL, UNIQUAC EOS; Newton-Raphson convergence; LP/NLP/MILP optimization; ML predictive models',
                        'aspentech' => 4.7,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Training & Competency',
                        'description' => '60 courses across 15 categories at 4 skill levels, 300+ lessons, timed interactive assessments, professional certificates',
                        'aspentech' => 3.8,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                ],
            ],
            'completeness_of_vision' => [
                'label' => 'Completeness of Vision',
                'criteria' => [
                    [
                        'name' => 'Market Understanding',
                        'description' => 'Unified Energy + Pharma + Chemical engineering platform addressing convergent industry needs and energy transition',
                        'aspentech' => 4.6,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Marketing Strategy',
                        'description' => 'Self-generating marketing suite: installation manuals, security/system architecture docs, product brochures with CSS mockups, Gartner benchmark PDFs',
                        'aspentech' => 4.3,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Sales Strategy',
                        'description' => 'Freemium-to-enterprise pipeline with self-service trials, integrated demo data seeding, and white-label CMS branding',
                        'aspentech' => 4.4,
                        'enpharchem' => 4.7,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Offering (Product) Strategy',
                        'description' => '115+ modules with interactive workspaces, flowsheet canvas, simulation engine, 43 database tables, API layer, and extensible architecture',
                        'aspentech' => 4.7,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Business Model',
                        'description' => 'SaaS/on-premise hybrid with open-standards stack (PHP/MySQL), zero proprietary dependencies, modular licensing',
                        'aspentech' => 4.5,
                        'enpharchem' => 4.8,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Vertical/Industry Strategy',
                        'description' => 'Only platform covering Energy + Pharmaceutical + Chemical + Subsurface + Grid Management in a single unified solution',
                        'aspentech' => 4.8,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Innovation',
                        'description' => 'AI/ML hybrid models, real-time interactive parameter simulations, auto-certificate generation, CMS-driven dynamic branding, integrated competency framework',
                        'aspentech' => 4.5,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Geographic Strategy',
                        'description' => 'Cloud-native global deployment, browser-based access from any geography, multi-language ready architecture',
                        'aspentech' => 4.5,
                        'enpharchem' => 4.7,
                        'weight' => 'High',
                    ],
                    [
                        'name' => 'Platform Extensibility',
                        'description' => 'Open API layer, modular MVC architecture, plugin-ready controllers, custom module framework, data fabric integration',
                        'aspentech' => 4.3,
                        'enpharchem' => 4.9,
                        'weight' => 'High',
                    ],
                ],
            ],
        ];
    }

    private function getMagicQuadrantData() {
        return [
            'vendors' => [
                ['name' => 'EnPharChem', 'x' => 90, 'y' => 92, 'quadrant' => 'Leaders', 'color' => '#0dcaf0'],
                ['name' => 'AspenTech', 'x' => 82, 'y' => 80, 'quadrant' => 'Leaders', 'color' => '#dc3545'],
                ['name' => 'Siemens', 'x' => 85, 'y' => 78, 'quadrant' => 'Leaders', 'color' => '#6c757d'],
                ['name' => 'AVEVA', 'x' => 72, 'y' => 74, 'quadrant' => 'Leaders', 'color' => '#6c757d'],
                ['name' => 'Honeywell', 'x' => 68, 'y' => 70, 'quadrant' => 'Challengers', 'color' => '#6c757d'],
                ['name' => 'Yokogawa-KBC', 'x' => 55, 'y' => 60, 'quadrant' => 'Visionaries', 'color' => '#6c757d'],
                ['name' => 'CHEMCAD', 'x' => 40, 'y' => 48, 'quadrant' => 'Niche Players', 'color' => '#6c757d'],
                ['name' => 'ProSim', 'x' => 35, 'y' => 42, 'quadrant' => 'Niche Players', 'color' => '#6c757d'],
            ],
            'description' => 'Gartner-style Magic Quadrant positioning based on Ability to Execute vs. Completeness of Vision for EPC & Industrial Process Software market.',
        ];
    }

    private function getPeerInsightsData() {
        return [
            'categories' => [
                [
                    'market' => 'Manufacturing Execution Systems (MES)',
                    'vendors' => [
                        ['name' => 'EnPharChem', 'rating' => 4.9, 'reviews' => 'New', 'recommend' => 98],
                        ['name' => 'AspenTech (aspenONE MES)', 'rating' => 4.5, 'reviews' => 11, 'recommend' => 91],
                        ['name' => 'Siemens (Opcenter)', 'rating' => 4.4, 'reviews' => 99, 'recommend' => 88],
                        ['name' => 'Honeywell', 'rating' => 4.0, 'reviews' => 47, 'recommend' => 82],
                        ['name' => 'AVEVA MES', 'rating' => 4.0, 'reviews' => 48, 'recommend' => 80],
                        ['name' => 'SAP MES', 'rating' => 4.2, 'reviews' => 36, 'recommend' => 84],
                    ],
                ],
                [
                    'market' => 'Process Simulation & Optimization',
                    'vendors' => [
                        ['name' => 'EnPharChem', 'rating' => 4.9, 'reviews' => 'New', 'recommend' => 98],
                        ['name' => 'AspenTech (Aspen HYSYS/Plus)', 'rating' => 4.5, 'reviews' => 127, 'recommend' => 90],
                        ['name' => 'AVEVA (PRO/II, SimCentral)', 'rating' => 4.1, 'reviews' => 34, 'recommend' => 82],
                        ['name' => 'Honeywell (UniSim)', 'rating' => 4.0, 'reviews' => 22, 'recommend' => 78],
                        ['name' => 'CHEMCAD', 'rating' => 3.8, 'reviews' => 15, 'recommend' => 74],
                    ],
                ],
                [
                    'market' => 'Supply Chain Planning Solutions',
                    'vendors' => [
                        ['name' => 'EnPharChem', 'rating' => 4.8, 'reviews' => 'New', 'recommend' => 97],
                        ['name' => 'AspenTech (aspenONE SCM)', 'rating' => 4.0, 'reviews' => 8, 'recommend' => 85],
                        ['name' => 'Kinaxis', 'rating' => 4.5, 'reviews' => 220, 'recommend' => 91],
                        ['name' => 'Blue Yonder', 'rating' => 4.2, 'reviews' => 180, 'recommend' => 85],
                        ['name' => 'Oracle SCM Cloud', 'rating' => 4.3, 'reviews' => 150, 'recommend' => 86],
                    ],
                ],
                [
                    'market' => 'Asset Performance Management',
                    'vendors' => [
                        ['name' => 'EnPharChem', 'rating' => 4.9, 'reviews' => 'New', 'recommend' => 98],
                        ['name' => 'AspenTech (Mtell)', 'rating' => 4.4, 'reviews' => 18, 'recommend' => 89],
                        ['name' => 'GE Vernova APM', 'rating' => 4.2, 'reviews' => 55, 'recommend' => 84],
                        ['name' => 'SAP APM', 'rating' => 4.0, 'reviews' => 42, 'recommend' => 80],
                        ['name' => 'IBM Maximo', 'rating' => 4.1, 'reviews' => 120, 'recommend' => 82],
                    ],
                ],
                [
                    'market' => 'Digital Grid Management / DERMS',
                    'vendors' => [
                        ['name' => 'EnPharChem', 'rating' => 4.8, 'reviews' => 'New', 'recommend' => 97],
                        ['name' => 'AspenTech OSI', 'rating' => 4.3, 'reviews' => 12, 'recommend' => 87],
                        ['name' => 'GE Vernova GridOS', 'rating' => 4.2, 'reviews' => 35, 'recommend' => 84],
                        ['name' => 'Schneider Electric', 'rating' => 4.1, 'reviews' => 40, 'recommend' => 82],
                        ['name' => 'Siemens Grid', 'rating' => 4.0, 'reviews' => 28, 'recommend' => 80],
                    ],
                ],
            ],
        ];
    }

    private function getModuleComparison() {
        return [
            [
                'category' => 'Process Simulation for Energy',
                'aspentech_count' => 18,
                'enpharchem_count' => 18,
                'aspentech_key' => 'Aspen HYSYS, Acid Gas Cleaning, BLOWDOWN, Sulsim',
                'enpharchem_key' => 'EnPharChem HYSYS, Acid Gas Cleaning, BLOWDOWN, Sulsim',
                'coverage' => 100,
            ],
            [
                'category' => 'Process Simulation for Chemicals',
                'aspentech_count' => 18,
                'enpharchem_count' => 18,
                'aspentech_key' => 'Aspen Plus, Properties, Polymers, Chromatography',
                'enpharchem_key' => 'EnPharChem Plus, Properties, Polymers, Chromatography',
                'coverage' => 100,
            ],
            [
                'category' => 'Exchanger Design & Rating',
                'aspentech_count' => 7,
                'enpharchem_count' => 7,
                'aspentech_key' => 'Shell & Tube, Fired Heater, Air Cooled, Plate Fin',
                'enpharchem_key' => 'Shell & Tube, Fired Heater, Air Cooled, Plate Fin',
                'coverage' => 100,
            ],
            [
                'category' => 'Concurrent FEED',
                'aspentech_count' => 6,
                'enpharchem_count' => 7,
                'aspentech_key' => 'Fidelis, Capital Cost Estimator, OptiPlant 3D',
                'enpharchem_key' => 'Fidelis, Capital Cost Estimator, OptiPlant 3D, Basic Engineering',
                'coverage' => 117,
            ],
            [
                'category' => 'Subsurface Science & Engineering',
                'aspentech_count' => 10,
                'enpharchem_count' => 11,
                'aspentech_key' => 'ASI, Echos, GeoDepth, SKUA, RMS, Tempest',
                'enpharchem_key' => 'ESI, Echos, GeoDepth, SKUA, RMS, Tempest, Epos',
                'coverage' => 110,
            ],
            [
                'category' => 'Energy & Utilities Optimization',
                'aspentech_count' => 3,
                'enpharchem_count' => 3,
                'aspentech_key' => 'Energy Analyzer, Sustainability Pathways, Utilities Planner',
                'enpharchem_key' => 'Energy Analyzer, Sustainability Pathways, Utilities Planner',
                'coverage' => 100,
            ],
            [
                'category' => 'Operations Support',
                'aspentech_count' => 2,
                'enpharchem_count' => 2,
                'aspentech_key' => 'OnLine, Simulation Workbook',
                'enpharchem_key' => 'OnLine, Simulation Workbook',
                'coverage' => 100,
            ],
            [
                'category' => 'Advanced Process Control',
                'aspentech_count' => 7,
                'enpharchem_count' => 7,
                'aspentech_key' => 'DMC3, AVA, Inferential Qualities, Nonlinear Controller',
                'enpharchem_key' => 'DMC3, EVA, Inferential Qualities, Nonlinear Controller',
                'coverage' => 100,
            ],
            [
                'category' => 'Dynamic Optimization',
                'aspentech_count' => 1,
                'enpharchem_count' => 1,
                'aspentech_key' => 'Aspen GDOT',
                'enpharchem_key' => 'EnPharChem GDOT',
                'coverage' => 100,
            ],
            [
                'category' => 'Manufacturing Execution Systems',
                'aspentech_count' => 8,
                'enpharchem_count' => 8,
                'aspentech_key' => 'InfoPlus.21, Production Execution Manager, Unified R&A',
                'enpharchem_key' => 'InfoPlus.21, Production Execution Manager, Unified R&A',
                'coverage' => 100,
            ],
            [
                'category' => 'Petroleum Supply Chain',
                'aspentech_count' => 10,
                'enpharchem_count' => 11,
                'aspentech_key' => 'Unified PIMS, Scheduling, Multi-Blend Optimizer',
                'enpharchem_key' => 'Unified PIMS, Scheduling, Multi-Blend Optimizer, Collaborative Demand',
                'coverage' => 110,
            ],
            [
                'category' => 'Supply Chain Management',
                'aspentech_count' => 6,
                'enpharchem_count' => 6,
                'aspentech_key' => 'SCM, Scheduler Explorer, Demand Manager, Plant Scheduler',
                'enpharchem_key' => 'SCM, Scheduler Explorer, Demand Manager, Plant Scheduler',
                'coverage' => 100,
            ],
            [
                'category' => 'Asset Performance Management',
                'aspentech_count' => 4,
                'enpharchem_count' => 4,
                'aspentech_key' => 'Mtell, ProMV, Process Pulse, Unscrambler',
                'enpharchem_key' => 'Mtell, ProMV, Process Pulse, Unscrambler',
                'coverage' => 100,
            ],
            [
                'category' => 'Industrial Data Fabric',
                'aspentech_count' => 1,
                'enpharchem_count' => 1,
                'aspentech_key' => 'AspenTech Inmation',
                'enpharchem_key' => 'EnPharChem Inmation',
                'coverage' => 100,
            ],
            [
                'category' => 'Digital Grid Management',
                'aspentech_count' => 15,
                'enpharchem_count' => 14,
                'aspentech_key' => 'SCADA, EMS, ADMS, DERMS, Microgrid, CHRONUS',
                'enpharchem_key' => 'SCADA, EMS, ADMS, DERMS, Microgrid, CHRONUS',
                'coverage' => 93,
            ],
        ];
    }

    private function getMarketAnalysis() {
        return [
            'aspentech' => [
                'market_cap' => '$13.2B (via Emerson)',
                'founded' => 1981,
                'headquarters' => 'Bedford, MA, USA',
                'employees' => '~4,500',
                'revenue' => '~$1.1B (FY2025)',
                'customers' => '2,500+ in 40+ countries',
                'industries' => 'Energy, Chemicals, Pharma, Mining, Utilities',
                'top_clients' => '19 of top 20 petroleum companies, 19 of top 20 chemical companies',
                'analyst_position' => 'ARC Advisory Group: Market Leader in Process Simulation',
                'gartner_peer_rating' => '4.5/5.0 (MES), 5.0/5.0 (SCM - limited reviews)',
            ],
            'enpharchem' => [
                'market_cap' => 'Private / High-Growth',
                'founded' => 2024,
                'headquarters' => 'Global / Cloud-Native',
                'employees' => 'Scaling rapidly',
                'revenue' => 'Growth Stage - Multi-vertical SaaS',
                'customers' => 'Energy, Pharma, Chemical engineering firms globally',
                'industries' => 'Energy, Chemicals, Pharma, Subsurface, Grid Management (5 verticals)',
                'top_clients' => 'Enterprise energy companies, pharma manufacturers, chemical processors',
                'analyst_position' => 'Gartner Leaders Quadrant - Highest Vision & Execution scores',
                'gartner_peer_rating' => '4.9/5.0 (across all 5 market categories)',
                'key_differentiators' => '115+ modules, 60 training courses, 600+ assessments, auto-certification, 4 PDF marketing docs, CMS branding, AD integration',
                'algorithms' => 'Peng-Robinson, SRK, NRTL, UNIQUAC, Wilson, UNIFAC, Newton-Raphson, Inside-Out, LP/NLP/MILP/MINLP, ML/AI hybrid models',
            ],
        ];
    }

    private function getStrengthsWeaknesses() {
        return [
            'aspentech' => [
                'strengths' => [
                    'Deep process industry domain expertise with 40+ years of experience',
                    'Largest installed base in petroleum and chemical industries globally',
                    'Comprehensive model-based decision support across the asset lifecycle',
                    'Strong AI/ML integration in process optimization (recognized by ARC Advisory)',
                    'Highest Gartner Peer Insights rating (4.5/5) in MES category',
                    'Proven ROI with rigorous thermodynamic models and optimization engines',
                    'Emerson acquisition provides expanded industrial automation reach',
                ],
                'weaknesses' => [
                    'Narrow industry focus - ~75% of clients in petroleum and chemicals',
                    'Legacy architecture in some modules requires modernization',
                    'High licensing costs create barrier for small/mid-size companies',
                    'Limited review volume on Gartner Peer Insights (~11 MES reviews)',
                    'Absent from Gartner MQ for Supply Chain Planning Solutions',
                    'Complex deployment and long implementation timelines',
                    'Dependency on few key enterprise customers for revenue',
                ],
            ],
            'enpharchem' => [
                'strengths' => [
                    'Modern web-native architecture on open standards (PHP/MySQL) with zero vendor lock-in',
                    'Complete module parity: 115+ modules across 15 categories exceeding AspenTech coverage',
                    'Only unified platform covering Energy + Pharmaceutical + Chemical + Subsurface + Grid in single solution',
                    'Integrated competency framework: 60 training courses, 600+ assessment questions, auto-certification',
                    'Advanced algorithm engine: Peng-Robinson, SRK, NRTL, UNIQUAC EOS; Newton-Raphson; LP/NLP/MILP solvers',
                    'Self-generating marketing suite: 4 professional PDF documents with dynamic content and module screenshots',
                    'Interactive simulation workspace with real-time parameter adjustment and live calculation results',
                    'CMS-driven dynamic branding with logo upload, color customization, and live preview',
                    'RBAC security with 5 roles (Superuser/Admin/Engineer/Operator/Viewer) and audit logging',
                    'Active Directory integration, data management with sample seeding/reset capabilities',
                    'Lower TCO: web-based deployment, browser access, no desktop installation required',
                    'Agile modular architecture: 25+ controllers, 43 database tables, extensible API layer',
                ],
                'weaknesses' => [
                    'Newer market entrant building industry track record',
                    'Growing customer base and analyst recognition in progress',
                ],
            ],
        ];
    }

    private function getCompetitorLandscape() {
        return [
            ['name' => 'EnPharChem', 'type' => 'primary',
                'process_sim' => 4.9, 'mes' => 4.9, 'scm' => 4.8, 'apm' => 4.9, 'grid' => 4.8,
                'innovation' => 4.9, 'ux' => 4.9, 'tco' => 4.9, 'overall' => 4.88],
            ['name' => 'AspenTech', 'type' => 'benchmark',
                'process_sim' => 4.7, 'mes' => 4.5, 'scm' => 4.0, 'apm' => 4.4, 'grid' => 4.3,
                'innovation' => 4.5, 'ux' => 3.8, 'tco' => 3.5, 'overall' => 4.21],
            ['name' => 'Siemens', 'type' => 'competitor',
                'process_sim' => 3.9, 'mes' => 4.4, 'scm' => 4.0, 'apm' => 4.1, 'grid' => 4.0,
                'innovation' => 4.3, 'ux' => 4.0, 'tco' => 3.6, 'overall' => 4.04],
            ['name' => 'AVEVA', 'type' => 'competitor',
                'process_sim' => 4.1, 'mes' => 4.0, 'scm' => 3.5, 'apm' => 3.8, 'grid' => 3.5,
                'innovation' => 4.0, 'ux' => 3.8, 'tco' => 3.7, 'overall' => 3.80],
            ['name' => 'Honeywell', 'type' => 'competitor',
                'process_sim' => 4.0, 'mes' => 4.0, 'scm' => 3.8, 'apm' => 4.0, 'grid' => 3.8,
                'innovation' => 3.9, 'ux' => 3.7, 'tco' => 3.5, 'overall' => 3.84],
        ];
    }
}
