<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1. ADMIN
        // ─────────────────────────────────────────────────────────────────────
        User::firstOrCreate(['email' => 'admin@clinic.com'], [
            'name'     => 'Admin',
            'password' => Hash::make('Admin@12345'),
            'role'     => 'admin',
        ]);

        // ─────────────────────────────────────────────────────────────────────
        // 2. DEPARTMENTS
        // ─────────────────────────────────────────────────────────────────────
        $deptDefs = [
            'General physician'    => 'General health services and checkups',
            'Gynecologist'         => 'Women health and reproductive care',
            'Dermatologist'        => 'Skin, hair and nail treatments',
            'Pediatricians'        => 'Children health care from birth to 18',
            'Neurologist'          => 'Brain and nervous system disorders',
            'Gastroenterologist'   => 'Digestive system and bowel care',
            'Cardiologist'         => 'Heart and cardiovascular care',
            'Orthopedist'          => 'Bones, joints and muscle care',
            'Ophthalmologist'      => 'Eye care and vision disorders',
            'Psychiatrist'         => 'Mental health and behavioral disorders',
            'Endocrinologist'      => 'Hormonal and metabolic disorders',
            'Pulmonologist'        => 'Lung and respiratory diseases',
            'Urologist'            => 'Urinary tract and male reproductive health',
            'Oncologist'           => 'Cancer diagnosis and treatment',
            'Rheumatologist'       => 'Arthritis and autoimmune diseases',
            'Dentist'              => 'Dental and oral health care',
            'ENT Specialist'       => 'Ear, nose and throat disorders',
            'Nephrologist'         => 'Kidney diseases and dialysis care',
            'Hematologist'         => 'Blood disorders and diseases',
            'Allergist'            => 'Allergies and immune system disorders',
        ];

        $deptMap = [];
        foreach ($deptDefs as $name => $desc) {
            $dept = Department::firstOrCreate(['name' => $name], ['description' => $desc]);
            $deptMap[$name] = $dept->id;
        }

        // ─────────────────────────────────────────────────────────────────────
        // 3. DOCTORS  — 2 per specialty, password: Admin@12345
        // ─────────────────────────────────────────────────────────────────────
        // Images cycle through doc1.png – doc15.png (stored in public/images/doctors/)
        $imgs = array_map(fn($n) => "/images/doctors/doc{$n}.png", range(1, 15));
        $imgIdx = 0;

        $doctors = [
            // General physician
            ['Dr. Sarah Al-Jowhari',   'dr.sarah.jowhari@clinic.com',   'General physician',  8,  60, 'Experienced general physician focused on preventive medicine and chronic disease management.'],
            ['Dr. Rami Abu Ghazala',   'dr.rami.ghazala@clinic.com',    'General physician',  5,  50, 'Family medicine practitioner with expertise in acute and chronic illness management.'],

            // Gynecologist
            ['Dr. Ahmed Al-Hassan',    'dr.ahmed.hassan@clinic.com',    'Gynecologist',      12,  80, 'Board-certified gynecologist specializing in maternal-fetal medicine and minimally invasive surgery.'],
            ['Dr. Nadia Farouk',       'dr.nadia.farouk@clinic.com',    'Gynecologist',       7,  75, 'Women\'s health specialist with a focus on high-risk pregnancy and fertility treatments.'],

            // Dermatologist
            ['Dr. Iman Al-Sheikh',     'dr.iman.sheikh@clinic.com',     'Dermatologist',      6,  70, 'Specialist in cosmetic and medical dermatology, treating all skin types and conditions.'],
            ['Dr. Layla Barakat',      'dr.layla.barakat@clinic.com',   'Dermatologist',      9,  80, 'Expert in acne, eczema, psoriasis, and laser skin treatments.'],

            // Pediatricians
            ['Dr. Mohammad Al-Touri',  'dr.mohammad.touri@clinic.com',  'Pediatricians',     10,  65, 'Compassionate pediatrician dedicated to children\'s health from newborn to adolescent.'],
            ['Dr. Hanaa Abu Saada',    'dr.hanaa.saada@clinic.com',     'Pediatricians',      4,  55, 'Specialist in childhood development, vaccinations, and pediatric nutrition.'],

            // Neurologist
            ['Dr. Layla Mansour',      'dr.layla.mansour@clinic.com',   'Neurologist',       15, 100, 'Highly experienced neurologist specializing in epilepsy, stroke, and neurodegenerative diseases.'],
            ['Dr. Kareem Al-Halabi',   'dr.kareem.halabi@clinic.com',   'Neurologist',       11,  90, 'Expert in headache disorders, multiple sclerosis, and Parkinson\'s disease management.'],

            // Gastroenterologist
            ['Dr. Jamal Al-Qartar',    'dr.jamal.qartar@clinic.com',    'Gastroenterologist', 9,  75, 'Expert in digestive health, treating IBS, Crohn\'s disease, and performing endoscopic procedures.'],
            ['Dr. Bariya Singh',       'dr.bariya.singh@clinic.com',    'Gastroenterologist', 6,  70, 'Specialist in liver diseases, gallbladder disorders, and colonoscopy procedures.'],

            // Cardiologist
            ['Dr. Omar Rashid',        'dr.omar.rashid@clinic.com',     'Cardiologist',      14,  95, 'Interventional cardiologist with expertise in angioplasty, stenting and heart failure management.'],
            ['Dr. Sawsan Layla',       'dr.sawsan.layla@clinic.com',    'Cardiologist',       8,  85, 'Specializes in echocardiography, arrhythmia management and preventive cardiology.'],

            // Orthopedist
            ['Dr. Marwan Adnan',       'dr.marwan.adnan@clinic.com',    'Orthopedist',       13,  90, 'Joint replacement and sports medicine specialist with extensive surgical experience.'],
            ['Dr. Yasmin Nour',        'dr.yasmin.nour@clinic.com',     'Orthopedist',        7,  80, 'Expert in spine disorders, fracture management and physiotherapy rehabilitation.'],

            // Ophthalmologist
            ['Dr. Grace Kim',          'dr.grace.kim@clinic.com',       'Ophthalmologist',   10,  75, 'Specialist in LASIK surgery, cataract removal and treatment of retinal diseases.'],
            ['Dr. Daoud Al-Bani',      'dr.daoud.bani@clinic.com',      'Ophthalmologist',    6,  65, 'Expert in glaucoma, diabetic retinopathy and pediatric eye care.'],

            // Psychiatrist
            ['Dr. Hanaa Aziz',         'dr.hanaa.aziz@clinic.com',      'Psychiatrist',      11,  90, 'Clinical psychiatrist specializing in depression, anxiety, bipolar disorder and PTSD.'],
            ['Dr. Tariq Abdullah',     'dr.tariq.abdullah@clinic.com',  'Psychiatrist',       8,  85, 'Expert in addiction psychiatry, cognitive behavioral therapy and psychopharmacology.'],

            // Endocrinologist
            ['Dr. Fatima Al-Zahra',    'dr.fatima.zahra@clinic.com',    'Endocrinologist',   12,  85, 'Diabetes and thyroid specialist with deep expertise in hormone disorders and metabolic diseases.'],
            ['Dr. Kareem Ibrahim',     'dr.kareem.ibrahim@clinic.com',  'Endocrinologist',    7,  80, 'Expert in obesity medicine, adrenal disorders and polycystic ovary syndrome (PCOS).'],

            // Pulmonologist
            ['Dr. Khalid Hassan',      'dr.khalid.hassan@clinic.com',   'Pulmonologist',      9,  80, 'Lung specialist treating asthma, COPD, pulmonary fibrosis and sleep apnea.'],
            ['Dr. Reem Al-Khadri',     'dr.reem.khadri@clinic.com',     'Pulmonologist',      5,  70, 'Expert in interventional pulmonology, bronchoscopy and critical care medicine.'],

            // Urologist
            ['Dr. Samer Al-Khatib',    'dr.samer.khatib@clinic.com',    'Urologist',         10,  85, 'Specialist in kidney stones, prostate conditions and minimally invasive urological surgery.'],
            ['Dr. Nina Patel',         'dr.nina.patel@clinic.com',      'Urologist',          7,  75, 'Female urology and pelvic floor specialist, expert in incontinence and bladder disorders.'],

            // Oncologist
            ['Dr. Majed Wahba',        'dr.majed.wahba@clinic.com',     'Oncologist',        16, 120, 'Medical oncologist with expertise in breast, lung and colorectal cancer chemotherapy protocols.'],
            ['Dr. Aisha Karimi',       'dr.aisha.karimi@clinic.com',    'Oncologist',        12, 110, 'Radiation oncologist specializing in targeted therapy and cancer immunotherapy.'],

            // Rheumatologist
            ['Dr. Elena Ruiz',         'dr.elena.ruiz@clinic.com',      'Rheumatologist',    10,  85, 'Expert in rheumatoid arthritis, lupus and other autoimmune rheumatic diseases.'],
            ['Dr. Hassan Yousef',      'dr.hassan.yousef@clinic.com',   'Rheumatologist',     6,  75, 'Specialist in gout, osteoporosis and fibromyalgia management.'],

            // Dentist
            ['Dr. Julia Al-Sawaliha',  'dr.julia.sawaliha@clinic.com',  'Dentist',            8,  55, 'General and cosmetic dentist specializing in implants, veneers and teeth whitening.'],
            ['Dr. Ali Mahmoud',        'dr.ali.mahmoud@clinic.com',     'Dentist',            5,  50, 'Orthodontist and dental surgeon expert in braces, Invisalign and oral surgery.'],

            // ENT Specialist
            ['Dr. Dina Al-Foster',     'dr.dina.foster@clinic.com',     'ENT Specialist',    11,  80, 'Ear, nose and throat specialist treating sinusitis, hearing loss and throat cancers.'],
            ['Dr. Rami Saleh',         'dr.rami.saleh@clinic.com',      'ENT Specialist',     7,  70, 'Expert in rhinoplasty, tonsillectomy and cochlear implant surgery.'],

            // Nephrologist
            ['Dr. Lina Majid',         'dr.lina.majid@clinic.com',      'Nephrologist',      13,  90, 'Kidney disease specialist managing CKD, dialysis and post-transplant care.'],
            ['Dr. Paul Nguyen',        'dr.paul.nguyen@clinic.com',     'Nephrologist',       9,  85, 'Expert in hypertension-related kidney disease and glomerulonephritis.'],

            // Hematologist
            ['Dr. Sophia Andersen',    'dr.sophia.andersen@clinic.com', 'Hematologist',      10,  90, 'Blood disorder specialist treating anemia, leukemia and clotting disorders.'],
            ['Dr. Tariq Bajwa',        'dr.tariq.bajwa@clinic.com',     'Hematologist',       7,  85, 'Expert in bone marrow transplantation and hemophilia management.'],

            // Allergist
            ['Dr. Mia Abu Hamdan',     'dr.mia.hamdan@clinic.com',      'Allergist',          8,  70, 'Allergy and immunology specialist treating food allergies, asthma and eczema.'],
            ['Dr. Ziad Haddad',        'dr.ziad.haddad@clinic.com',     'Allergist',          5,  65, 'Expert in skin allergy testing, immunotherapy and anaphylaxis management.'],
        ];

        $createdDoctors = [];
        foreach ($doctors as [$name, $email, $speciality, $exp, $fee, $bio]) {
            $image = $imgs[$imgIdx % 15];
            $imgIdx++;

            $user = User::firstOrCreate(['email' => $email], [
                'name'     => $name,
                'password' => Hash::make('Admin@12345'),
                'role'     => 'doctor',
            ]);
            // Always keep name in sync
            $user->update(['name' => $name]);

            $deptId = $deptMap[$speciality] ?? array_values($deptMap)[0];

            $doctor = Doctor::firstOrCreate(['user_id' => $user->id], [
                'department_id'    => $deptId,
                'specialization'   => $speciality,
                'bio'              => $bio,
                'consultation_fee' => $fee,
                'experience_years' => $exp,
                'image'            => $image,
                'address'          => 'غزة، فلسطين - Gaza, Palestine',
                'is_approved'      => true,
            ]);
            // Always keep image in sync
            $doctor->update(['image' => $image]);

            $createdDoctors[] = $doctor;
        }

        // ─────────────────────────────────────────────────────────────────────
        // 4. PATIENTS  — password: Admin@12345
        // ─────────────────────────────────────────────────────────────────────
        $patients = [
            ['Yousef Abu Samra',    'patient@clinic.com',         '1990-05-15'],
            ['Mariam Al-Ghazali',   'mariam.ghazali@example.com', '1985-08-22'],
            ['Daoud Hamdan',        'daoud.hamdan@example.com',   '1995-03-10'],
            ['Sara Ali',            'sara.ali@example.com',       '1998-11-30'],
            ['Omar Khalid',         'omar.khalid@example.com',    '1988-07-04'],
            ['Layla Hassan',        'layla.hassan@example.com',   '1993-02-18'],
            ['Jamal Al-Abyad',      'jamal.abyad@example.com',   '1979-12-01'],
            ['Reem Saleh',          'reem.saleh@example.com',     '2000-09-25'],
        ];

        $createdPatients = [];
        foreach ($patients as [$name, $email, $dob]) {
            $user = User::firstOrCreate(['email' => $email], [
                'name'     => $name,
                'password' => Hash::make('Admin@12345'),
                'role'     => 'patient',
            ]);
            $user->update(['name' => $name]);

            $patient = Patient::firstOrCreate(['user_id' => $user->id], [
                'date_of_birth' => $dob,
                'address'       => 'غزة، فلسطين - Gaza, Palestine',
            ]);
            $createdPatients[] = $patient;
        }

        // ─────────────────────────────────────────────────────────────────────
        // 5. APPOINTMENTS
        // ─────────────────────────────────────────────────────────────────────
        $times    = ['09:00:00', '09:30:00', '10:00:00', '10:30:00', '11:00:00', '14:00:00', '14:30:00', '15:00:00', '15:30:00', '16:00:00'];
        $statuses = ['completed', 'completed', 'confirmed', 'pending', 'cancelled'];
        $base     = now();

        foreach (range(0, 29) as $i) {
            $patient = $createdPatients[$i % count($createdPatients)];
            $doctor  = $createdDoctors[$i  % count($createdDoctors)];
            $daysOff = ($i % 2 === 0) ? -($i + 1) : ($i + 1);
            $date    = $base->copy()->addDays($daysOff)->format('Y-m-d');
            $time    = $times[$i % count($times)];
            $status  = $statuses[$i % count($statuses)];

            $exists = Appointment::where('doctor_id', $doctor->id)
                ->where('appointment_date', $date)
                ->where('appointment_time', $time)
                ->exists();

            if (!$exists) {
                Appointment::create([
                    'patient_id'       => $patient->id,
                    'doctor_id'        => $doctor->id,
                    'appointment_date' => $date,
                    'appointment_time' => $time,
                    'fee'              => $doctor->consultation_fee,
                    'status'           => $status,
                    'title'            => 'Consultation',
                    'description'      => 'Regular medical consultation',
                ]);
            }
        }

        $this->command->info('');
        $this->command->info('✅  Database seeded!');
        $this->command->info('');
        $this->command->info('  Login accounts (password: Admin@12345)');
        $this->command->info('  ─────────────────────────────────────────');
        $this->command->info('  ADMIN   → admin@clinic.com');
        $this->command->info('  DOCTOR  → doctor@clinic.com');
        $this->command->info('  PATIENT → patient@clinic.com');
        $this->command->info('  ─────────────────────────────────────────');
        $this->command->info('  40 doctors · 8 patients · 30 appointments');
        $this->command->info('');
    }
}
