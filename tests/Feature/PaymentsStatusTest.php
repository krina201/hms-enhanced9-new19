<?php
use App\Models\{Bill,BillItem,Payment,User,Patient};
use App\Enums\BillStatusEnum;

beforeEach(function(){
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('updates bill status to PARTIAL and PAID based on payments', function(){
    $patient = Patient::factory()->create();
    $bill = Bill::factory()->create(['patient_id'=>$patient->id, 'grand_total'=>1180, 'subtotal'=>1000, 'tax'=>180, 'discount'=>0, 'status'=>BillStatusEnum::ISSUED]);
    BillItem::create(['bill_id'=>$bill->id,'description'=>'Consult','qty'=>1,'unit_price'=>1000,'tax_rate'=>18,'discount'=>0]);

    expect($bill->fresh()->status)->toEqual(BillStatusEnum::ISSUED);

    Payment::create(['bill_id'=>$bill->id,'payment_date'=>now(),'amount'=>500,'method'=>'CASH','reference'=>'A']);
    expect($bill->fresh()->status)->toEqual(BillStatusEnum::PARTIAL);

    Payment::create(['bill_id'=>$bill->id,'payment_date'=>now(),'amount'=>680,'method'=>'CASH','reference'=>'B']);
    expect($bill->fresh()->status)->toEqual(BillStatusEnum::PAID);
});