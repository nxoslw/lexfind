<?php

namespace Database\Seeders;

use App\Models\Lawyer;
use App\Models\LawyerCase;
use App\Models\User;
use Illuminate\Database\Seeder;

class LawyerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Brian F. Barakat, Esq.
        $barakatUser = User::where('email', 'barakat@lexfind.com')->first();

        $barakat = Lawyer::create([
            'user_id' => $barakatUser ? $barakatUser->id : null,
            'name' => 'Brian F. Barakat, Esq.',
            'slug' => \Illuminate\Support\Str::slug('Brian F. Barakat, Esq.'),
            'title' => 'Founding Shareholder & Managing Partner',
            'firm' => 'Barakat + Bossa, PLLC',
            'city' => 'Coral Gables',
            'state' => 'FL',
            'specialty' => 'Complex Commercial Litigation, Corporate Fraud, Shareholder Disputes',
            'bio' => 'Brian Barakat is a Board-Certified Business Litigator, a rare status held by fewer than 250 attorneys in Florida. He launched his legal career as a public criminal prosecutor in the Economic Crimes Division of the Miami-Dade State Attorney\'s Office. This background provides an exceptional technical edge in handling civil fraud tracking, corporate asset freeze maneuvers, and forensic account reconstructions.',
            'avatar_color' => '#1E3A54',
            'initials' => 'BB',
            'email' => 'barakat@b2b.legal',
            'phone' => '+1 (305) 444-3114',
            'website' => 'b2b.legal',
            'linkedin' => 'https://www.linkedin.com/in/brian-barakat-b620025',
            'years_experience' => 25,
            'cases_count' => 1600,
            'cases_won' => 1450,
            'cases_lost' => 45,
            'cases_settled' => 105,
            'cases_active' => 40,
            'financial_recovery' => '$50M+',
            'fee_structure' => 'Hourly Retainer / Corporate Blended Rates',
            'is_certified' => true,
            'rating' => 5.0,
            'availability' => 'available',
            'criminal_record' => 'CLEARED',
            'bar_discipline' => 'CLEARED',
            'trial_style' => 'Known in court corridors as a precise, highly calculating, and aggressive "trial-first" litigator. Because of his foundational decade as a frontline prosecutor tracking financial crimes, his cross-examination style relies heavily on trapping witnesses using unexpected forensic bank ledgers and digital metadata.',
            'peer_reviews' => [
                'rating' => '5.0 / 5.0',
                'source' => 'Martindale-Hubbell Peer Review',
                'quote' => 'Brian is an exceptionally ethical yet zealous courtroom advocate. His preparation is unmatched, especially in corporate separation accounting.',
                'author' => 'Chambers USA Directory Review Panel',
            ],
            'recent_activity' => [
                ['date' => 'Apr 15, 2026', 'title' => 'Legal 500 Miami Elite Rankings', 'desc' => 'Formally named and ranked in the Legal 500 Miami Elite Rankings for high-stakes Commercial Disputes.'],
                ['date' => 'Apr 10, 2026', 'title' => 'Litigation Reversal Breakthrough', 'desc' => 'Executed a highly tactical emergency Lis Pendens real estate freezing maneuver that forced an absolute, rapid capital recovery following a fractured multi-million dollar medical business acquisition dispute.'],
                ['date' => 'Jan 08, 2026', 'title' => 'Appellate Victory', 'desc' => 'Secured total victory in the Third DCA in Trident Real Estate, Inc. v. Sonny & Ricardo, LLC, vacating a lower-court default judgment.'],
            ],
            'practice_areas' => ['Business Litigation', 'Corporate Law', 'Real Estate'],
            'trial_style_details' => [
                'approach' => 'Surgical, evidence-first litigation strategy. Barakat leads with procedural mastery and forensic accounting to dismantle opposing damages claims before trial begins.',
                'forensics' => 'Known for arithmetical validation of unliquidated damages — a specialty that has produced multiple appellate reversals where trial courts failed to apply mandatory evidentiary standards.',
            ],
        ]);

        // Seed cases for Brian F. Barakat
        $case1 = LawyerCase::create([
            'lawyer_id' => $barakat->id,
            'name' => 'Trident Real Estate, Inc. v. Sonny & Ricardo, LLC et al.',
            'slug' => \Illuminate\Support\Str::slug('Trident Real Estate, Inc. v. Sonny & Ricardo, LLC et al.'),
            'case_number' => 'Case No. 3D2025-0116',
            'jurisdiction' => 'District Court of Appeal of Florida, Third District',
            'type' => 'civil',
            'type_label' => 'Commercial Real Estate Fraud / Procedural Due Process',
            'status' => 'decided',
            'year' => 2026,
            'court' => 'Third DCA',
            'won_party' => 'Barakat\'s Client (The Defendant / Appellant)',
            'motions_count' => 1,
            'motion_success_rate' => '100%',
            'timeline' => [
                ['date' => 'May 12, 2024', 'badge' => 'initiated', 'badgeLabel' => 'Initiated', 'title' => 'Emergency Motion to Set Aside Default Final Judgment', 'action' => 'Barakat files an intensive evidentiary motion demonstrating a complete lack of valid service and unliquidated damage errors in the trial court.', 'status' => 'initiated', 'context' => ''],
                ['date' => 'August 19, 2024', 'badge' => 'denied', 'badgeLabel' => 'Denied (0%)', 'title' => 'Order Denying Defendant’s Motion to Set Aside Default', 'action' => 'The lower circuit court judge signs an order denying the defensive emergency filing, prompting Barakat to invoke immediate appellate jurisdiction.', 'status' => 'denied', 'context' => 'Denied at Trial Level', 'contextClass' => 'denied'],
                ['date' => 'January 15, 2025', 'badge' => 'procedural', 'badgeLabel' => 'Procedural Step', 'title' => 'Notice of Appeal Filed', 'action' => 'The appellate track is formally activated with the Third DCA clerk to freeze potential execution attempts on corporate bank accounts.', 'status' => 'procedural', 'context' => ''],
                ['date' => 'April 22, 2025', 'badge' => 'brief', 'badgeLabel' => 'Procedural Brief', 'title' => 'Appellant’s Main Omnibus Brief Filed', 'action' => 'Barakat’s appellate team submits their primary brief, introducing comprehensive jurisdictional authority showing the trial court violated clear civil procedure rules.', 'status' => 'brief', 'context' => ''],
                ['date' => 'November 5, 2025', 'badge' => 'oral', 'badgeLabel' => 'Oral Argument', 'title' => 'Oral Arguments Executed', 'action' => 'Barakat appears live before a three-judge panel to detail the lack of arithmetical validation for the alleged fraud damages.', 'status' => 'oral', 'context' => ''],
                ['date' => 'January 8, 2026', 'badge' => 'victory', 'badgeLabel' => 'Granted on Appeal (100%)', 'title' => 'Mandate & Reversal Opinion Signed', 'action' => 'Full appellate victory. The Third DCA issues a written opinion completely vacating the lower court default judgment and restoring the client\'s right to defend the case.', 'status' => 'victory', 'context' => 'Won on Appeal', 'contextClass' => 'victory'],
            ],
            'motions' => [
                ['date' => 'May 12, 2024', 'motion' => 'Emergency Motion to Set Aside Default Final Judgment', 'details' => 'Demonstrated complete lack of valid service and unliquidated damage errors in the trial court.', 'result' => 'denied', 'resultLabel' => 'Denied'],
                ['date' => 'Nov 5, 2025', 'motion' => 'Appellate Oral Reversal Petition', 'details' => 'Argued before Three-Judge panel on lack of arithmetical verification for damages.', 'result' => 'accepted', 'resultLabel' => 'Granted'],
            ],
            'parties' => [
                ['role' => 'Plaintiff / Appellee', 'name' => 'Trident Real Estate, Inc.', 'status' => 'lost', 'isWinner' => false],
                ['role' => 'Defendant / Appellant', 'name' => 'Sonny & Ricardo, LLC et al.', 'status' => 'won', 'isWinner' => true],
            ],
            'issues' => [
                ['num' => 1, 'title' => 'Lack of Valid Service', 'desc' => 'Service was not properly executed under Florida civil procedure guidelines, rendering default void.'],
                ['num' => 2, 'title' => 'Unliquidated Damages Validation', 'desc' => 'Trial court calculated damages without arithmetical backing or evidentiary validation hearings.'],
            ],
            'documents' => [
                ['name' => 'Notice of Appeal (Third DCA)', 'date' => 'Jan 15, 2025', 'type' => 'procedural', 'typeLabel' => 'Procedural'],
                ['name' => 'Appellant Main Brief (Omnibus)', 'date' => 'Apr 22, 2025', 'type' => 'brief', 'typeLabel' => 'Brief'],
                ['name' => 'Opinion Vacating Judgment (Third DCA)', 'date' => 'Jan 8, 2026', 'type' => 'judgment', 'typeLabel' => 'Judgment'],
            ],
            'summary' => 'A high-stakes commercial property dispute where the trial court entered a default judgment against a corporate defendant without proper service. On appeal, the Third DCA reversed and vacated the judgment, restoring the defendant\'s rights to answer the complaint.',
            'key_finding' => 'The Third DCA panel affirmed that a lack of arithmetical validation for alleged fraud damages represents a structural due process violation requiring vacating.',
            'rate_boxes' => ['fail' => '0%', 'win' => '100%', 'failSub' => 'at Trial Level', 'winSub' => 'on Appeal'],
            'next_steps' => [
                ['icon' => '⚖️', 'title' => 'Trial Court Remand', 'desc' => 'Case is remanded to the lower court where Sonny & Ricardo, LLC will file its formal answer to the complaint.'],
                ['icon' => '💬', 'title' => 'Settlement Discussions', 'desc' => 'Parties are initiating mediation to explore potential commercial settlement paths.'],
            ],
        ]);

        LawyerCase::create([
            'lawyer_id' => $barakat->id,
            'name' => 'Delta Bay Investments, LLC v. 2010 NW 107 Ave LLC',
            'slug' => \Illuminate\Support\Str::slug('Delta Bay Investments, LLC v. 2010 NW 107 Ave LLC'),
            'case_number' => 'Case No. 3D25-1923',
            'jurisdiction' => 'District Court of Appeal of Florida, Third District',
            'type' => 'corporate',
            'type_label' => 'Commercial Real Estate / Landlord Enforcement',
            'status' => 'decided',
            'year' => 2025,
            'court' => 'Third DCA',
            'won_party' => 'Barakat\'s Client (The Landlord / Plaintiff / Appellee)',
            'motions_count' => 3,
            'motion_success_rate' => '100%',
            'timeline' => [],
            'motions' => [],
            'parties' => [],
            'issues' => [],
            'documents' => [],
            'summary' => 'Commercial landlord eviction enforcement where the tenant defaulted on depositing rent into the court registry. Barakat bypassed trial, obtained summary possession, and successfully defeated the tenant\'s appellate stay filings.',
            'key_finding' => 'Registry default under Florida statutes constitutes an absolute waiver of tenant defenses.',
        ]);

        LawyerCase::create([
            'lawyer_id' => $barakat->id,
            'name' => 'Parisi v. De Kingston',
            'slug' => \Illuminate\Support\Str::slug('Parisi v. De Kingston'),
            'case_number' => '314 So. 3d 656',
            'jurisdiction' => 'District Court of Appeal of Florida, Third District',
            'type' => 'corporate',
            'type_label' => 'Corporate Governance / Validity of Power of Attorney',
            'status' => 'decided',
            'year' => 2021,
            'court' => 'Third DCA',
            'won_party' => 'Barakat\'s Client (The Plaintiff / Appellee)',
            'motions_count' => 1,
            'motion_success_rate' => '100%',
            'summary' => 'Defensive corporate shielding. Barakat successfully defeated the opposing party\'s motion to dismiss, proving the power of attorney was validly executed to initiate litigation.',
            'key_finding' => 'Appellate court mandate sustained the original commercial complaint.',
        ]);

        // 2. Giacomo Bossa, Esq.
        $bossaUser = User::where('email', 'bossa@lexfind.com')->first();

        $bossa = Lawyer::create([
            'user_id' => $bossaUser ? $bossaUser->id : null,
            'name' => 'Giacomo Bossa, Esq.',
            'slug' => \Illuminate\Support\Str::slug('Giacomo Bossa, Esq.'),
            'title' => 'Co-Founding Partner',
            'firm' => 'Barakat + Bossa, PLLC',
            'city' => 'Coral Gables',
            'state' => 'FL',
            'specialty' => 'International Business Arbitration, Commercial Real Estate Litigation',
            'bio' => 'Giacomo Bossa holds an extraordinarily rare distinction in Florida: he is Dual Board Certified in both Business Litigation AND Real Estate Law. Highly fluent across multi-continent legal systems, Bossa holds credentials under both Civil Law (continental Europe) and Common Law (United States) frameworks. He regularly manages high-value cross-border disputes and real estate syndication asset trials.',
            'avatar_color' => '#065F46',
            'initials' => 'GB',
            'email' => 'service@b2b.legal',
            'phone' => '+1 (305) 444-3114',
            'website' => 'b2b.legal',
            'linkedin' => 'https://www.linkedin.com/in/giacomo-bossa-4340a6b',
            'years_experience' => 18,
            'cases_count' => 1200,
            'cases_won' => 1100,
            'cases_lost' => 30,
            'cases_settled' => 70,
            'cases_active' => 45,
            'financial_recovery' => 'Multi-million dollar portfolio isolations',
            'fee_structure' => 'Corporate Billable Hour Retainer Matrix',
            'is_certified' => true,
            'rating' => 5.0,
            'availability' => 'available',
            'criminal_record' => 'CLEARED',
            'bar_discipline' => 'CLEARED',
            'trial_style' => 'Described by international corporate entities in Chambers USA reviews as having a remarkable "ability to navigate complex multi-jurisdictional legal issues with extreme precision." His behavior is deeply academic and polyglot, leaning heavily into international treaty technicalities (such as the Hague Convention or the New York Arbitration Convention) to strip court jurisdiction away from opposing parties before a trial even begins.',
            'peer_reviews' => [
                'rating' => '5.0 / 5.0',
                'source' => 'Chambers USA Client Review',
                'quote' => 'Giacomo is incredibly skilled at international disputes. He understands civil law codes and common law boundaries seamlessly.',
                'author' => 'General Counsel, Multinational Shipping Corp',
            ],
            'recent_activity' => [
                ['date' => 'Jan 21, 2026', 'title' => 'Landmark Appellate Victory', 'desc' => 'Won a landmark written opinion in Wepard v. Diaz Reus Targ LLP, validating electronic service of process via email under modern Florida statutes.'],
                ['date' => 'Apr 10, 2026', 'title' => 'Federal Settlement Conference', 'desc' => 'Managed complex Marques de Ivanrey v. Soriano civil RICO and trademark trial in New York federal courts, culminating in intensive settlement conference.'],
                ['date' => 'Feb 05, 2025', 'title' => 'Appellate Cargo Default Victory', 'desc' => 'Secured total affirmation in Transcad Corp v. VCB International Logistic, blocking logistics provider\'s neglect reversal attempts.'],
            ],
            'practice_areas' => ['International Arbitration', 'Real Estate', 'Corporate Law'],
            'trial_style_details' => [
                'approach' => 'Highly academic and polyglot approach. Bossa regularly leverages complex multi-jurisdictional legal codes, treaty technicalities (such as the Hague or New York Conventions) to isolate assets.',
                'global' => 'Functions as a legal bridge between the US and European commercial landscapes. Dual-certified in Business Litigation and Real Estate Law.',
            ],
        ]);

        // Seed cases for Giacomo Bossa
        LawyerCase::create([
            'lawyer_id' => $bossa->id,
            'name' => 'Wepard Corporation Limited, et al. v. Diaz Reus Targ LLP',
            'slug' => \Illuminate\Support\Str::slug('Wepard Corporation Limited, et al. v. Diaz Reus Targ LLP'),
            'case_number' => 'Case No. 3D25-0252',
            'jurisdiction' => 'District Court of Appeal of Florida, Third District',
            'type' => 'civil',
            'type_label' => 'International Service of Process Challenge / Hague Convention',
            'status' => 'decided',
            'year' => 2026,
            'court' => 'Third DCA',
            'won_party' => 'Bossa\'s Client (The Appellee)',
            'motions_count' => 2,
            'motion_success_rate' => '100%',
            'timeline' => [
                ['date' => 'Nov 7, 2024', 'badge' => 'initiated', 'badgeLabel' => 'Initiated', 'title' => 'Omnibus Motion to Quash Service of Process', 'action' => 'Opposing European parties challenge email notice, asserting it explicitly violated Malta declarations under the Hague Convention.', 'status' => 'initiated', 'context' => ''],
                ['date' => 'Jan 11, 2025', 'badge' => 'procedural', 'badgeLabel' => 'Trial Order', 'title' => 'Order Denying Motion to Quash', 'action' => 'Trial court judge officially denies the defense challenge, validating the modern email service protocol under Florida law.', 'status' => 'procedural', 'context' => ''],
                ['date' => 'Jan 21, 2026', 'badge' => 'victory', 'badgeLabel' => 'Appellate Victory', 'title' => 'Landmark Written Opinion Issued', 'action' => 'Third DCA panel issues final opinion affirming Bossa\'s victory, ruling email bypass is allowed if not explicitly barred by treaty.', 'status' => 'victory', 'context' => 'Final Affirmance', 'contextClass' => 'victory'],
            ],
            'summary' => 'A landmark international service of process dispute where the court validated electronic service of process via email on foreign corporations based in Malta, bypassing traditional Hague Convention delays if not explicitly prohibited by the receiving country.',
            'key_finding' => 'Florida foreign service statutes allow email notice if the target country does not register an active objection under the treaty.',
        ]);

        LawyerCase::create([
            'lawyer_id' => $bossa->id,
            'name' => 'Felipe Thomas y de la Gandara, 5th Marques de Ivanrey v. Marco Antonio Soriano',
            'slug' => \Illuminate\Support\Str::slug('Felipe Thomas y de la Gandara, 5th Marques de Ivanrey v. Marco Antonio Soriano'),
            'case_number' => 'Case No. 2:25-cv-02242-FB-JMW',
            'jurisdiction' => 'U.S. District Court for the Eastern District of New York',
            'type' => 'corporate',
            'type_label' => 'Federal Commercial Fraud, Civil RICO, and Trademark Tort',
            'status' => 'active',
            'year' => 2026,
            'court' => 'E.D.N.Y.',
            'won_party' => 'Pending (Strategic settlement talks active)',
            'motions_count' => 2,
            'motion_success_rate' => '50%',
            'summary' => 'Federal tort litigation involving claims of false advertising and trademark infringement for classic electric motorcycles. Bossa challenged venue and personal jurisdiction, leading to court-ordered settlement conferences.',
            'key_finding' => 'Federal magistrate judge ordered live settlement exchanges after dismissing unviable international elements.',
        ]);
    }
}
