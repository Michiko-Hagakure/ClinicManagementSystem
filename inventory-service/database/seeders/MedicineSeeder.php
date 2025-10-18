<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Medicine;
use App\Models\LowStockAlert;

class MedicineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medicines = [
            // CAPSULES/TABLETS
            ['name' => 'SIMVASTATIN', 'category' => 'Capsules/Tablets', 'dosage' => '40mg', 'price' => 15.00, 'stock_quantity' => 100],
            ['name' => 'SN-PHOS', 'category' => 'Capsules/Tablets', 'dosage' => 'N/A', 'price' => 10.00, 'stock_quantity' => 100],
            ['name' => 'SODIUM ASCORBATE WITH ZINC', 'category' => 'Capsules/Tablets', 'dosage' => 'N/A', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'TAMSULOSIN', 'category' => 'Capsules/Tablets', 'dosage' => '400mcg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'VITAMIN B COMPLEX', 'category' => 'Capsules/Tablets', 'dosage' => 'N/A', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'VITASTRESS VIT+IRON', 'category' => 'Capsules/Tablets', 'dosage' => 'N/A', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'ACETYLCYSTEINE SACHET', 'category' => 'Capsules/Tablets', 'dosage' => '600mg', 'price' => 35.00, 'stock_quantity' => 100],
            ['name' => 'ALLUPURINOL', 'category' => 'Capsules/Tablets', 'dosage' => '100mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'AMBROXOL', 'category' => 'Capsules/Tablets', 'dosage' => '75mg', 'price' => 25.00, 'stock_quantity' => 100],
            ['name' => 'AMLODIPINE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 8.00, 'stock_quantity' => 100],
            ['name' => 'AMLODIPINE', 'category' => 'Capsules/Tablets', 'dosage' => '5mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'ATORVASTATIN', 'category' => 'Capsules/Tablets', 'dosage' => '40mg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'ATORVASTATIN', 'category' => 'Capsules/Tablets', 'dosage' => '20mg', 'price' => 15.00, 'stock_quantity' => 100],
            ['name' => 'ATORVASTATIN', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 9.00, 'stock_quantity' => 100],
            ['name' => 'BETAHISTINE HYDROCLHORIDE', 'category' => 'Capsules/Tablets', 'dosage' => '16mg', 'price' => 32.00, 'stock_quantity' => 100],
            ['name' => 'BETAHISTINE HYDROCLHORIDE', 'category' => 'Capsules/Tablets', 'dosage' => '24mg', 'price' => 48.00, 'stock_quantity' => 100],
            ['name' => 'BISACODYL', 'category' => 'Capsules/Tablets', 'dosage' => '5mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'BUTAMIRATE CITRATE', 'category' => 'Capsules/Tablets', 'dosage' => '50mg', 'price' => 14.00, 'stock_quantity' => 100],
            ['name' => 'CARBOCISTEINE', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'CELECOXIB', 'category' => 'Capsules/Tablets', 'dosage' => '200mg', 'price' => 16.00, 'stock_quantity' => 100],
            ['name' => 'CETIRIZINE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 6.00, 'stock_quantity' => 100],
            ['name' => 'COLCHICINE', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 8.00, 'stock_quantity' => 100],
            ['name' => 'CLONIDINE', 'category' => 'Capsules/Tablets', 'dosage' => '75mCg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'CLOPIDOGREL', 'category' => 'Capsules/Tablets', 'dosage' => '75mg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'DIOSMIN + HESPERIDINE', 'category' => 'Capsules/Tablets', 'dosage' => '450/50mg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'DIPHENHYDRAMINE', 'category' => 'Capsules/Tablets', 'dosage' => '50mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'DOMPERIDONE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 8.00, 'stock_quantity' => 100],
            ['name' => 'FEBUXOSTAT', 'category' => 'Capsules/Tablets', 'dosage' => '40mg', 'price' => 25.00, 'stock_quantity' => 100],
            ['name' => 'FENOFIBRATES', 'category' => 'Capsules/Tablets', 'dosage' => '200mg', 'price' => 16.00, 'stock_quantity' => 100],
            ['name' => 'FUROSEMIDE', 'category' => 'Capsules/Tablets', 'dosage' => '40mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'FUROSEMIDE', 'category' => 'Capsules/Tablets', 'dosage' => '20mg', 'price' => 4.00, 'stock_quantity' => 100],
            ['name' => 'FERROUS SULFATE', 'category' => 'Capsules/Tablets', 'dosage' => '250mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'FOLLIC ACID', 'category' => 'Capsules/Tablets', 'dosage' => '5mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'GLICLAZIDE', 'category' => 'Capsules/Tablets', 'dosage' => '80mg', 'price' => 26.00, 'stock_quantity' => 100],
            ['name' => 'GLICLAZIDE', 'category' => 'Capsules/Tablets', 'dosage' => '60mg', 'price' => 22.00, 'stock_quantity' => 100],
            ['name' => 'GUAIFENESIN', 'category' => 'Capsules/Tablets', 'dosage' => '100mg', 'price' => 6.00, 'stock_quantity' => 100],
            ['name' => 'HYOSINE n BUTYLBROMIDE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 10.00, 'stock_quantity' => 100],
            ['name' => 'LOPERAMIDE', 'category' => 'Capsules/Tablets', 'dosage' => '2mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'LORATADINE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'LOSARTAN', 'category' => 'Capsules/Tablets', 'dosage' => '100mg', 'price' => 8.00, 'stock_quantity' => 100],
            ['name' => 'LOSARTAN', 'category' => 'Capsules/Tablets', 'dosage' => '50mg', 'price' => 8.00, 'stock_quantity' => 100],
            ['name' => 'MEFENAMIC ACID', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 6.00, 'stock_quantity' => 100],
            ['name' => 'METFORMIN', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 6.00, 'stock_quantity' => 100],
            ['name' => 'METOCLOPRAMIDE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'MULTI VITA', 'category' => 'Capsules/Tablets', 'dosage' => 'N/A', 'price' => 15.00, 'stock_quantity' => 100],
            ['name' => 'OMEPRAZOLE', 'category' => 'Capsules/Tablets', 'dosage' => '40mg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'OMEPRAZOLE', 'category' => 'Capsules/Tablets', 'dosage' => '20mg', 'price' => 15.00, 'stock_quantity' => 100],
            ['name' => 'PARACETAMOL', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'PARACETAMOL+TRAMADOL', 'category' => 'Capsules/Tablets', 'dosage' => '325mg/37.5mg', 'price' => 25.00, 'stock_quantity' => 100],
            ['name' => 'PARACETAMOL+ORPHENADRINE CITRATE', 'category' => 'Capsules/Tablets', 'dosage' => '650mg/35mg', 'price' => 35.00, 'stock_quantity' => 100],
            ['name' => 'PREDNISONE', 'category' => 'Capsules/Tablets', 'dosage' => '10mg', 'price' => 7.00, 'stock_quantity' => 100],
            ['name' => 'PREDNISONE', 'category' => 'Capsules/Tablets', 'dosage' => '5mg', 'price' => 4.00, 'stock_quantity' => 100],
            ['name' => 'SAMBONG', 'category' => 'Capsules/Tablets', 'dosage' => '500mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'CALCIUM+ VIT.D3', 'category' => 'Capsules/Tablets', 'dosage' => '600/10mg', 'price' => 7.00, 'stock_quantity' => 100],
            
            // ANTIBIOTICS (TABS/CAPS)
            ['name' => 'AMOXICILLIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'AMOXICILLIN', 'category' => 'Antibiotics', 'dosage' => '250mg', 'price' => 5.00, 'stock_quantity' => 100],
            ['name' => 'AZITHROMYCIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'CEFALEXIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'CEFUROXIME', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 45.00, 'stock_quantity' => 100],
            ['name' => 'CIPROFLOXACIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'CLINDAMYCIN', 'category' => 'Antibiotics', 'dosage' => '300mg', 'price' => 20.00, 'stock_quantity' => 100],
            ['name' => 'CLOXACILLIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 12.00, 'stock_quantity' => 100],
            ['name' => 'CO-AMOXICLAV', 'category' => 'Antibiotics', 'dosage' => '625mg', 'price' => 30.00, 'stock_quantity' => 100],
            ['name' => 'COTRIMOXAZOLE', 'category' => 'Antibiotics', 'dosage' => '800mg/160mg', 'price' => 10.00, 'stock_quantity' => 100],
            ['name' => 'DOXYCYCLINE', 'category' => 'Antibiotics', 'dosage' => '100mg', 'price' => 10.00, 'stock_quantity' => 100],
            ['name' => 'ERYTHROMYCIN', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 10.00, 'stock_quantity' => 100],
            ['name' => 'METRONIDAZOLE', 'category' => 'Antibiotics', 'dosage' => '500mg', 'price' => 7.00, 'stock_quantity' => 100],
            
            // SUSPENSION
            ['name' => 'ASCORBIC ACID', 'category' => 'Suspension', 'dosage' => '100mg/10mg/5ml', 'price' => 80.00, 'stock_quantity' => 100],
            ['name' => 'AMOXICILLIN', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'AMOXICILLIN', 'category' => 'Suspension', 'dosage' => '100mg/ml', 'price' => 55.00, 'stock_quantity' => 100],
            ['name' => 'AMBROXOL', 'category' => 'Suspension', 'dosage' => '30mg/5ml', 'price' => 80.00, 'stock_quantity' => 100],
            ['name' => 'AMBROXOL', 'category' => 'Suspension', 'dosage' => '15mg/5ml', 'price' => 70.00, 'stock_quantity' => 100],
            ['name' => 'AMBROXOL', 'category' => 'Suspension', 'dosage' => '6mg/ml', 'price' => 55.00, 'stock_quantity' => 100],
            ['name' => 'CARBOCISTEINE', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 70.00, 'stock_quantity' => 100],
            ['name' => 'CARBOCISTEINE', 'category' => 'Suspension', 'dosage' => '100mg/5ml', 'price' => 55.00, 'stock_quantity' => 100],
            ['name' => 'CEFACLOR MONOHYDRATE', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 250.00, 'stock_quantity' => 100],
            ['name' => 'CEFALEXIN', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 70.00, 'stock_quantity' => 100],
            ['name' => 'CEFALEXIN', 'category' => 'Suspension', 'dosage' => '125mg/5ml', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'CEFALEXIN', 'category' => 'Suspension', 'dosage' => '100mg/ml', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'CEFIXIME', 'category' => 'Suspension', 'dosage' => '100mg/5ml', 'price' => 280.00, 'stock_quantity' => 100],
            ['name' => 'CEFUROXIME', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 280.00, 'stock_quantity' => 100],
            ['name' => 'CETIRIZINE', 'category' => 'Suspension', 'dosage' => '5mg/5ml', 'price' => 135.00, 'stock_quantity' => 100],
            ['name' => 'CETIRIZINE', 'category' => 'Suspension', 'dosage' => '2.5mg/5ml', 'price' => 100.00, 'stock_quantity' => 100],
            ['name' => 'CLOXACILLIN', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 180.00, 'stock_quantity' => 100],
            ['name' => 'CLOXACILLIN', 'category' => 'Suspension', 'dosage' => '125mg/5ml', 'price' => 150.00, 'stock_quantity' => 100],
            ['name' => 'CO-AMOXICLAV', 'category' => 'Suspension', 'dosage' => '457mg/5ml', 'price' => 260.00, 'stock_quantity' => 100],
            ['name' => 'COTRIMOXAZOLE', 'category' => 'Suspension', 'dosage' => '200mg/40mg/5ml', 'price' => 75.00, 'stock_quantity' => 100],
            ['name' => 'DICYCLOVERINE HYDROCHLORIDE', 'category' => 'Suspension', 'dosage' => '10mg/5ml', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'LORATADINE', 'category' => 'Suspension', 'dosage' => '5mg/5ml', 'price' => 140.00, 'stock_quantity' => 100],
            ['name' => 'MEFENAMIC ACID', 'category' => 'Suspension', 'dosage' => '50mg', 'price' => 55.00, 'stock_quantity' => 100],
            ['name' => 'METRONIDAZOLE', 'category' => 'Suspension', 'dosage' => '125mg/5ml', 'price' => 80.00, 'stock_quantity' => 100],
            ['name' => 'MULTIVITAMINS', 'category' => 'Suspension', 'dosage' => 'N/A', 'price' => 80.00, 'stock_quantity' => 100],
            ['name' => 'PARACETAMOL', 'category' => 'Suspension', 'dosage' => '250mg/5ml', 'price' => 60.00, 'stock_quantity' => 100],
            ['name' => 'PARACETAMOL', 'category' => 'Suspension', 'dosage' => '125mg/5ml', 'price' => 55.00, 'stock_quantity' => 100],
            ['name' => 'PREDNISONE', 'category' => 'Suspension', 'dosage' => '10mg/5ml', 'price' => 150.00, 'stock_quantity' => 100],
            ['name' => 'SALBUTAMOL GUAIFENESIN', 'category' => 'Suspension', 'dosage' => '1mg/50mg/5ml', 'price' => 55.00, 'stock_quantity' => 100],
            
            // DROPS/OINTMENT/CREAM/NEBULE
            ['name' => 'TOBRAMYCIN DROPS', 'category' => 'Drops/Ointment/Cream', 'dosage' => '3mg/ml', 'price' => 180.00, 'stock_quantity' => 100],
            ['name' => 'TOBRAMYCIN+DEXAMETHASONE DROPS', 'category' => 'Drops/Ointment/Cream', 'dosage' => '3mg/1ml', 'price' => 250.00, 'stock_quantity' => 100],
            ['name' => 'CLOBETASOL PROPIONATE CREAM', 'category' => 'Drops/Ointment/Cream', 'dosage' => '0.05%', 'price' => 200.00, 'stock_quantity' => 100],
            ['name' => 'HYDROCORTISONE CREAM', 'category' => 'Drops/Ointment/Cream', 'dosage' => '10mg/g', 'price' => 200.00, 'stock_quantity' => 100],
            ['name' => 'MUPIROCIN OINT', 'category' => 'Drops/Ointment/Cream', 'dosage' => '20mg/g 5g', 'price' => 200.00, 'stock_quantity' => 100],
            ['name' => 'SALBUTAMOL NEBULE', 'category' => 'Nebule', 'dosage' => 'N/A', 'price' => 25.00, 'stock_quantity' => 100],
            ['name' => 'SALBUTAMOL + IPRATOMIUM NEBULE', 'category' => 'Nebule', 'dosage' => 'N/A', 'price' => 25.00, 'stock_quantity' => 100],
            ['name' => 'SALMETEROL FLUTICASONE', 'category' => 'Nebule', 'dosage' => '25+125', 'price' => 350.00, 'stock_quantity' => 100],
            ['name' => 'ORAL REHYDRATION SALT(ORS)', 'category' => 'Others', 'dosage' => '1 sachet', 'price' => 10.00, 'stock_quantity' => 100],
            
            // IV MEDS
            ['name' => 'TRAMADOL', 'category' => 'IV Meds', 'dosage' => '100mg/2ml amp', 'price' => 50.00, 'stock_quantity' => 100],
            ['name' => 'OMEPRAZOLE', 'category' => 'IV Meds', 'dosage' => '40mg IV', 'price' => 50.00, 'stock_quantity' => 100],
            ['name' => 'OMEPRAZOLE SODIUM', 'category' => 'IV Meds', 'dosage' => '40MG/IV', 'price' => 50.00, 'stock_quantity' => 100],
            ['name' => 'HYDROCORTISONE', 'category' => 'IV Meds', 'dosage' => '100mg vial', 'price' => 100.00, 'stock_quantity' => 100],
            ['name' => 'HYDROCORTISONE', 'category' => 'IV Meds', 'dosage' => '250mg vial', 'price' => 150.00, 'stock_quantity' => 100],
            ['name' => 'HYOSCINE', 'category' => 'IV Meds', 'dosage' => '20mg/ml amp', 'price' => 50.00, 'stock_quantity' => 100],
        ];

        foreach ($medicines as $medicine) {
            Medicine::create($medicine);
        }
    }
}
