<?php
use App\Models\{Admission,User};
use App\Http\Controllers\AdmissionDischargePdfController;

beforeEach(function(){
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders discharge summary response', function(){
    $a = \App\Models\Admission::factory()->create();
    $ctrl = new AdmissionDischargePdfController();
    $resp = $ctrl->__invoke($a->id);
    expect($resp)->not->toBeNull();
});