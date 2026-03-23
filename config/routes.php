<?php
/**
 * EnPharChem Platform - Route Definitions
 */

return [
    // Auth routes
    'login' => ['controller' => 'AuthController', 'action' => 'login'],
    'logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'register' => ['controller' => 'AuthController', 'action' => 'register'],

    // Dashboard
    '' => ['controller' => 'DashboardController', 'action' => 'index'],
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],

    // Projects
    'projects' => ['controller' => 'ProjectController', 'action' => 'index'],
    'projects/create' => ['controller' => 'ProjectController', 'action' => 'create'],
    'projects/view' => ['controller' => 'ProjectController', 'action' => 'view'],
    'projects/edit' => ['controller' => 'ProjectController', 'action' => 'edit'],
    'projects/delete' => ['controller' => 'ProjectController', 'action' => 'delete'],

    // Module categories
    'modules' => ['controller' => 'ModuleController', 'action' => 'index'],
    'modules/category' => ['controller' => 'ModuleController', 'action' => 'category'],
    'modules/view' => ['controller' => 'ModuleController', 'action' => 'view'],
    'modules/launch' => ['controller' => 'ModuleController', 'action' => 'launch'],

    // Simulations
    'simulations' => ['controller' => 'SimulationController', 'action' => 'index'],
    'simulations/create' => ['controller' => 'SimulationController', 'action' => 'create'],
    'simulations/run' => ['controller' => 'SimulationController', 'action' => 'run'],
    'simulations/view' => ['controller' => 'SimulationController', 'action' => 'view'],
    'simulations/results' => ['controller' => 'SimulationController', 'action' => 'results'],

    // Process Simulation Energy
    'process-sim-energy' => ['controller' => 'ProcessSimEnergyController', 'action' => 'index'],
    'process-sim-energy/hysys' => ['controller' => 'ProcessSimEnergyController', 'action' => 'hysys'],
    'process-sim-energy/module' => ['controller' => 'ProcessSimEnergyController', 'action' => 'module'],

    // Process Simulation Chemicals
    'process-sim-chemicals' => ['controller' => 'ProcessSimChemicalsController', 'action' => 'index'],
    'process-sim-chemicals/plus' => ['controller' => 'ProcessSimChemicalsController', 'action' => 'plus'],
    'process-sim-chemicals/module' => ['controller' => 'ProcessSimChemicalsController', 'action' => 'module'],

    // Exchanger Design
    'exchanger-design' => ['controller' => 'ExchangerDesignController', 'action' => 'index'],
    'exchanger-design/module' => ['controller' => 'ExchangerDesignController', 'action' => 'module'],

    // Concurrent FEED
    'concurrent-feed' => ['controller' => 'ConcurrentFeedController', 'action' => 'index'],
    'concurrent-feed/module' => ['controller' => 'ConcurrentFeedController', 'action' => 'module'],

    // Subsurface Science
    'subsurface-science' => ['controller' => 'SubsurfaceScienceController', 'action' => 'index'],
    'subsurface-science/module' => ['controller' => 'SubsurfaceScienceController', 'action' => 'module'],

    // Energy Optimization
    'energy-optimization' => ['controller' => 'EnergyOptimizationController', 'action' => 'index'],
    'energy-optimization/module' => ['controller' => 'EnergyOptimizationController', 'action' => 'module'],

    // Operations Support
    'operations-support' => ['controller' => 'OperationsSupportController', 'action' => 'index'],
    'operations-support/module' => ['controller' => 'OperationsSupportController', 'action' => 'module'],

    // Advanced Process Control
    'advanced-process-control' => ['controller' => 'APCController', 'action' => 'index'],
    'advanced-process-control/module' => ['controller' => 'APCController', 'action' => 'module'],

    // Dynamic Optimization
    'dynamic-optimization' => ['controller' => 'DynamicOptimizationController', 'action' => 'index'],
    'dynamic-optimization/module' => ['controller' => 'DynamicOptimizationController', 'action' => 'module'],

    // MES
    'mes' => ['controller' => 'MESController', 'action' => 'index'],
    'mes/module' => ['controller' => 'MESController', 'action' => 'module'],

    // Petroleum Supply Chain
    'petroleum-supply-chain' => ['controller' => 'PetroleumSCController', 'action' => 'index'],
    'petroleum-supply-chain/module' => ['controller' => 'PetroleumSCController', 'action' => 'module'],

    // Supply Chain Management
    'supply-chain-mgmt' => ['controller' => 'SupplyChainController', 'action' => 'index'],
    'supply-chain-mgmt/module' => ['controller' => 'SupplyChainController', 'action' => 'module'],

    // APM
    'apm' => ['controller' => 'APMController', 'action' => 'index'],
    'apm/module' => ['controller' => 'APMController', 'action' => 'module'],

    // Industrial Data Fabric
    'industrial-data-fabric' => ['controller' => 'DataFabricController', 'action' => 'index'],
    'industrial-data-fabric/module' => ['controller' => 'DataFabricController', 'action' => 'module'],

    // Digital Grid Management
    'digital-grid-mgmt' => ['controller' => 'DigitalGridController', 'action' => 'index'],
    'digital-grid-mgmt/module' => ['controller' => 'DigitalGridController', 'action' => 'module'],

    // API endpoints
    'api/simulations' => ['controller' => 'ApiController', 'action' => 'simulations'],
    'api/components' => ['controller' => 'ApiController', 'action' => 'components'],
    'api/flowsheet' => ['controller' => 'ApiController', 'action' => 'flowsheet'],
    'api/dashboard-stats' => ['controller' => 'ApiController', 'action' => 'dashboardStats'],

    // Control Panel
    'control-panel' => ['controller' => 'ControlPanelController', 'action' => 'index'],
    'control-panel/active-directory' => ['controller' => 'ControlPanelController', 'action' => 'activeDirectory'],
    'control-panel/cms' => ['controller' => 'ControlPanelController', 'action' => 'cmsPages'],
    'control-panel/cms/edit' => ['controller' => 'ControlPanelController', 'action' => 'cmsPageEdit'],
    'control-panel/marketing' => ['controller' => 'ControlPanelController', 'action' => 'marketingMaterial'],
    'control-panel/training' => ['controller' => 'ControlPanelController', 'action' => 'trainingMaterial'],
    'control-panel/data-management' => ['controller' => 'ControlPanelController', 'action' => 'dataManagement'],
    'control-panel/load-sample-data' => ['controller' => 'ControlPanelController', 'action' => 'loadSampleData'],
    'control-panel/reset-sample-data' => ['controller' => 'ControlPanelController', 'action' => 'resetSampleData'],

    // Marketing Material PDFs
    'marketing/installation-manual' => ['controller' => 'MarketingDocsController', 'action' => 'installationManual'],
    'marketing/security-architecture' => ['controller' => 'MarketingDocsController', 'action' => 'securityArchitecture'],
    'marketing/system-architecture' => ['controller' => 'MarketingDocsController', 'action' => 'systemArchitecture'],
    'marketing/product-brochure' => ['controller' => 'MarketingDocsController', 'action' => 'productBrochure'],
    'marketing/seed-materials' => ['controller' => 'MarketingDocsController', 'action' => 'seedMaterials'],

    // Benchmark
    'benchmark' => ['controller' => 'BenchmarkController', 'action' => 'index'],
    'benchmark/pdf' => ['controller' => 'BenchmarkController', 'action' => 'pdf'],

    // Admin
    'admin' => ['controller' => 'AdminController', 'action' => 'index'],
    'admin/users' => ['controller' => 'AdminController', 'action' => 'users'],
    'admin/modules' => ['controller' => 'AdminController', 'action' => 'modules'],
    'admin/settings' => ['controller' => 'AdminController', 'action' => 'settings'],
];
