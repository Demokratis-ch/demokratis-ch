<?php

namespace App\Service;

use EasyRdf\Graph;
use EasyRdf\RdfNamespace;
use EasyRdf\Sparql\Client;
use EasyRdf\Sparql\Result;

class SparqlService
{
    private Client $client;

    public function __construct()
    {
        RdfNamespace::set('dbc', 'http://dbpedia.org/resource/Category:');
        RdfNamespace::set('dbpedia', 'http://dbpedia.org/resource/');
        RdfNamespace::set('dbo', 'http://dbpedia.org/ontology/');
        RdfNamespace::set('dbp', 'http://dbpedia.org/property/');

        $this->client = new Client('https://fedlex.data.admin.ch/sparqlendpoint');
    }

    public function getConsultations(string $filter = 'Laufend'): Graph|Result
    {
        if ($filter === 'all') {
            $prefix = '#';
        }

        return $this->client->query(
            '
            PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
            PREFIX owl: <http://www.w3.org/2002/07/owl#>
            PREFIX jolux: <http://data.legilux.public.lu/resource/ontology/jolux#>
            PREFIX f:<https://fedlex.data.admin.ch/eli/dl/proj/6014/115/>
            PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
            PREFIX status: <https://fedlex.data.admin.ch/vocabulary/consultation-status/>
            
            select *
            
            WHERE {
              ?s a jolux:Consultation.
              ?s jolux:eventId ?id.
              ?s jolux:eventDescription ?desc.
              ?s jolux:eventTitle ?title.
              ?s jolux:consultationStatus ?status.
              ?status skos:prefLabel ?statLabel.
              ?s jolux:hasSubTask ?subTask.
              ?subTask rdf:type ?subTaskType
              OPTIONAL {?subTask jolux:eventStartDate ?startDate.}
              OPTIONAL {?subTask jolux:eventEndDate ?endDate.}
              ?subTask jolux:institutionInChargeOfTheEvent ?office.
              ?subTask jolux:institutionInChargeOfTheEventLevel2 ?institution.
              ?office skos:prefLabel ?officeLabel.
              ?institution skos:prefLabel ?institutionLabel.
            
            #  This filters out other subtasks, especially for "Abgeschlossen", e.g. jolux:ConsultationPreparation, 
            #  jolux:PositionStatementPublication, jolux:ResultOfAConsultationPublication
              VALUES (?status ?myStatusLabel ?subTaskType) {
                (status:0 "In Vorbereitung" UNDEF)
                (status:1 "Geplant" jolux:ConsultationPreparation)
                (status:2 "Laufen" jolux:ConsultationPhase)
                (status:3 "Abgeschlossen - Stellungnahmen und/oder Ergebnisberichts abwarten" jolux:ConsultationPhase)
                (status:4 "Abgeschlossen - Ergebnisbericht abwarten" jolux:ConsultationPhase)
                (status:5 "Abgeschlossen" jolux:ConsultationPhase)
              }
                          
              FILTER (lang(?desc) = "de")
              FILTER (lang(?title) = "de")
              FILTER (lang(?statLabel) = "de")
              FILTER (lang(?officeLabel) = "de")
              FILTER (lang(?institutionLabel) = "de")
              FILTER (?subTaskType NOT IN (jolux:Event, jolux:LegislativeTask, jolux:ConsultationTask))
              '.$prefix.'Filter (STRSTARTS(?myStatusLabel, "'.$filter.'"))
            
            } ORDER BY DESC(?id)
            '
        );
    }

    public function getConsultationDocuments(string $fedlexId, bool $proposal = false): Graph|Result
    {
        // Vernehmlassungsvorlagen or other documents
        $type = $proposal ? 'opinionIsAboutDraftDocument' : 'opinionHasDraftRelatedDocument';

        return $this->client->query(
            '
            select * WHERE {
            
              <https://fedlex.data.admin.ch/eli/dl/'.$fedlexId.'> <http://data.legilux.public.lu/resource/ontology/jolux#hasSubTask> ?subTask.
  			  ?subTask <http://data.legilux.public.lu/resource/ontology/jolux#'.$type.'> ?document.
			  ?document <http://data.legilux.public.lu/resource/ontology/jolux#title> ?documentTitle.
              
              FILTER (lang(?documentTitle) = "de")
            } ORDER BY DESC(?id)
            '
        );
    }
}
