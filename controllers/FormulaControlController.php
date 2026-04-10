<?php
/**
 * EnPharChem - Formulae Control Sheets Controller
 * Interactive calculation sheets for Energy, Pharmaceutical, and Chemical engineering mixes
 */

class FormulaControlController extends BaseController {

    public function index() {
        $this->view('control-panel/formula-control/index', [
            'pageTitle' => 'Formulae Control Sheets',
        ]);
    }

    public function energyMix() {
        $this->view('control-panel/formula-control/energy-mix', [
            'pageTitle' => 'Energy Chemical Mix Control Sheet',
        ]);
    }

    public function pharmaMix() {
        $this->view('control-panel/formula-control/pharma-mix', [
            'pageTitle' => 'Pharmaceutical Chemical Mix Control Sheet',
        ]);
    }

    public function chemicalMix() {
        $this->view('control-panel/formula-control/chemical-mix', [
            'pageTitle' => 'Chemical Engineering Mix Control Sheet',
        ]);
    }
}
