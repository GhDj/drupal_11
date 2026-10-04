<?php

/**
 * @file
 * Creates a sample annual report with chapters for testing.
 *
 * Usage: drush php:script create_report.php
 */

use Drupal\node\Entity\Node;

$chapters = [
  [
    'title' => 'Bedrohungslage im Überblick',
    'body' => 'Die Bedrohungslage im Bereich der Cybersicherheit hat sich im Berichtszeitraum weiter verschärft. Die Anzahl der registrierten Cyberangriffe stieg im Vergleich zum Vorjahr um 25 Prozent. Insbesondere Ransomware-Angriffe auf Unternehmen und öffentliche Einrichtungen haben deutlich zugenommen. Die Angreifer nutzen zunehmend komplexe Methoden und automatisierte Tools, um Schwachstellen in IT-Systemen auszunutzen.',
  ],
  [
    'title' => 'Ransomware und Erpressung',
    'body' => 'Ransomware bleibt eine der größten Bedrohungen für die IT-Sicherheit in Deutschland. Im Berichtszeitraum wurden zahlreiche Angriffe auf Krankenhäuser, Kommunalverwaltungen und mittelständische Unternehmen verzeichnet. Die Angreifer verschlüsseln dabei nicht nur Daten, sondern drohen zunehmend auch mit der Veröffentlichung gestohlener Informationen. Die durchschnittliche Lösegeldforderung lag bei 1,2 Millionen Euro.',
  ],
  [
    'title' => 'Schwachstellen in Software und Hardware',
    'body' => 'Die Anzahl der bekannt gewordenen Schwachstellen in Software- und Hardwareprodukten erreichte im Berichtszeitraum einen neuen Höchststand. Das BSI hat über 2.000 kritische Schwachstellen identifiziert und entsprechende Warnungen veröffentlicht. Besonders betroffen waren Betriebssysteme, Webbrowser und Netzwerkkomponenten. Die schnelle Verfügbarkeit von Patches und Updates bleibt eine zentrale Herausforderung.',
  ],
  [
    'title' => 'Cyberangriffe auf kritische Infrastrukturen',
    'body' => 'Kritische Infrastrukturen wie Energieversorgung, Gesundheitswesen und Transportwesen waren im Berichtszeitraum verstärkt Ziel von Cyberangriffen. Die Angriffe reichten von Distributed-Denial-of-Service-Attacken bis hin zu gezielten Spionageoperationen. Das BSI hat gemeinsam mit den Betreibern kritischer Infrastrukturen Maßnahmen zur Stärkung der Resilienz entwickelt und implementiert.',
  ],
  [
    'title' => 'Phishing und Social Engineering',
    'body' => 'Phishing-Angriffe haben sich weiter professionalisiert. Die Angreifer nutzen zunehmend personalisierte E-Mails und gefälschte Webseiten, die kaum von legitimen Angeboten zu unterscheiden sind. Im Berichtszeitraum wurden über 100.000 Phishing-Webseiten identifiziert und gemeldet. Besonders häufig wurden dabei Banken, Online-Händler und Behörden imitiert.',
  ],
  [
    'title' => 'Künstliche Intelligenz und Cybersicherheit',
    'body' => 'Der Einsatz von Künstlicher Intelligenz im Bereich der Cybersicherheit nimmt sowohl auf Seiten der Verteidiger als auch der Angreifer zu. KI-gestützte Systeme können Anomalien schneller erkennen und auf Bedrohungen reagieren. Gleichzeitig nutzen Angreifer KI-Tools zur Erstellung überzeugender Phishing-Nachrichten und zur automatisierten Schwachstellensuche. Das BSI forscht aktiv an KI-basierten Sicherheitslösungen.',
  ],
  [
    'title' => 'Cloud-Sicherheit',
    'body' => 'Mit der zunehmenden Nutzung von Cloud-Diensten durch Unternehmen und Behörden steigen auch die Anforderungen an die Cloud-Sicherheit. Im Berichtszeitraum wurden mehrere Sicherheitsvorfälle bei Cloud-Anbietern registriert, die zu Datenverlust und Betriebsunterbrechungen führten. Das BSI hat den Cloud-Computing-Compliance-Criteria-Katalog aktualisiert und neue Empfehlungen für die sichere Cloud-Nutzung veröffentlicht.',
  ],
  [
    'title' => 'IT-Sicherheit im Gesundheitswesen',
    'body' => 'Das Gesundheitswesen war im Berichtszeitraum besonders stark von Cyberangriffen betroffen. Mehrere Krankenhäuser mussten ihren Betrieb vorübergehend einschränken. Die Digitalisierung im Gesundheitssektor schreitet voran, wobei der Schutz von Patientendaten und die Verfügbarkeit medizinischer Systeme höchste Priorität haben. Das BSI unterstützt Krankenhäuser und Gesundheitseinrichtungen mit speziellen Sicherheitsrichtlinien.',
  ],
  [
    'title' => 'Industrielle Steuerungssysteme und OT-Sicherheit',
    'body' => 'Die Sicherheit von industriellen Steuerungssystemen und Operational Technology bleibt eine zentrale Herausforderung. Im Berichtszeitraum wurden gezielte Angriffe auf Produktionsanlagen und Versorgungsnetze verzeichnet. Die zunehmende Vernetzung von IT- und OT-Systemen erfordert ganzheitliche Sicherheitskonzepte. Das BSI hat neue Standards für die OT-Sicherheit entwickelt.',
  ],
  [
    'title' => 'Cyberspionage und staatliche Akteure',
    'body' => 'Staatlich gesteuerte Cyberoperationen stellen weiterhin eine erhebliche Bedrohung dar. Im Berichtszeitraum wurden mehrere Spionagekampagnen aufgedeckt, die auf Regierungseinrichtungen, Forschungsinstitute und Unternehmen der Verteidigungsindustrie abzielten. Die Angreifer nutzten dabei hochentwickelte Schadsoftware und Zero-Day-Schwachstellen. Die internationale Zusammenarbeit bei der Abwehr solcher Bedrohungen wurde intensiviert.',
  ],
  [
    'title' => 'Digitale Identitäten und Authentifizierung',
    'body' => 'Der Schutz digitaler Identitäten gewinnt weiter an Bedeutung. Im Berichtszeitraum wurden zahlreiche Fälle von Identitätsdiebstahl registriert. Das BSI fördert den Einsatz von Multi-Faktor-Authentifizierung und passwortlosen Verfahren. Die eID-Funktion des Personalausweises wird zunehmend in digitalen Verwaltungsdiensten eingesetzt und bietet ein hohes Sicherheitsniveau.',
  ],
  [
    'title' => 'Empfehlungen und Ausblick',
    'body' => 'Das BSI empfiehlt allen Organisationen, ihre IT-Sicherheitsmaßnahmen kontinuierlich zu überprüfen und anzupassen. Regelmäßige Updates, Mitarbeiterschulungen und die Implementierung von Notfallplänen sind grundlegende Maßnahmen. Für das kommende Jahr erwartet das BSI eine weitere Zunahme von KI-gestützten Angriffen sowie neue Herausforderungen durch Quantencomputing. Die Stärkung der digitalen Souveränität Deutschlands bleibt ein zentrales Ziel.',
  ],
];

// Create the entry page (book root).
$entryPage = Node::create([
  'type' => 'entry_page',
  'title' => 'Jahreslagebericht 2025',
  'body' => [
    'value' => '<p>Der Bericht zur Lage der IT-Sicherheit in Deutschland beschreibt und analysiert die aktuelle Bedrohungslage, Schwachstellen und Angriffsmethoden im Bereich der Informationssicherheit. Er richtet sich an Entscheidungsträger in Politik, Wirtschaft und Verwaltung sowie an die interessierte Öffentlichkeit.</p>',
    'format' => 'full_html',
  ],
  'langcode' => 'de',
  'status' => 1,
]);
$entryPage->book = [
  'bid' => 'new',
  'pid' => -1,
  'weight' => 0,
];
$entryPage->save();

$bid = $entryPage->id();
echo "Created entry page: {$entryPage->label()} (nid: {$bid})\n";

// Create report pages (chapters).
foreach ($chapters as $weight => $chapter) {
  $node = Node::create([
    'type' => 'report_page',
    'title' => $chapter['title'],
    'body' => [
      'value' => '<p>' . $chapter['body'] . '</p>',
      'format' => 'full_html',
    ],
    'langcode' => 'de',
    'status' => 1,
  ]);
  $node->book = [
    'bid' => $bid,
    'pid' => $bid,
    'weight' => $weight,
  ];
  $node->save();
  echo "Created chapter: {$chapter['title']} (nid: {$node->id()})\n";
}

echo "\nDone! Created 1 entry page + " . count($chapters) . " chapters.\n";
echo "Entry page: /node/{$bid}\n";
echo "\nNext steps:\n";
echo "  drush search-api:reset-tracker default\n";
echo "  drush search-api:index default\n";
echo "  drush cr\n";
