<?php

namespace App\Services;

use Carbon\Carbon;
use Mtownsend\XmlToArray\XmlToArray;

class SaftService
{
    // Saft parsed data. 
    protected $data;

    // Time (minutes) the data is cached for.
    protected $data_cache_time = 14400;

    /**
     *  Parse the saf-t and cache it.
     */
    function __construct()
    {
        set_time_limit(300);
        ini_set('memory_limit','512M');
        //\Cache::forget('saft_data');

        $this->data = \Cache::remember('saft_data', $this->data_cache_time * 60, function () use (&$saft) {
            $saft_xml = \Storage::disk('local')->get('saft-pt-demo2.xml');
            $saft = XmlToArray::convert($saft_xml);
            $accounts = $this->parseAccounts($saft);
            return [
                'company_information' => $this->parseCompanyInformation($saft),
                'trial_balance_sheet' => [
                    'all_year' => $this->parseTrialBalanceSheet($saft, $accounts, 1, 12),
                    'trimester' => [
                        '1' => $this->parseTrialBalanceSheet($saft, $accounts, 1, 3),
                        '2' => $this->parseTrialBalanceSheet($saft, $accounts, 4, 6),
                        '3' => $this->parseTrialBalanceSheet($saft, $accounts, 7, 9),
                        '4' => $this->parseTrialBalanceSheet($saft, $accounts, 10, 12),
                    ],
                    'month' => [
                        '1' => $this->parseTrialBalanceSheet($saft, $accounts, 1, 1),
                        '2' => $this->parseTrialBalanceSheet($saft, $accounts, 2, 2),
                        '3' => $this->parseTrialBalanceSheet($saft, $accounts, 3, 3),
                        '4' => $this->parseTrialBalanceSheet($saft, $accounts, 4, 4),
                        '5' => $this->parseTrialBalanceSheet($saft, $accounts, 5, 5),
                        '6' => $this->parseTrialBalanceSheet($saft, $accounts, 6, 6),
                        '7' => $this->parseTrialBalanceSheet($saft, $accounts, 7, 7),
                        '8' => $this->parseTrialBalanceSheet($saft, $accounts, 8, 8),
                        '9' => $this->parseTrialBalanceSheet($saft, $accounts, 9, 9),
                        '10' => $this->parseTrialBalanceSheet($saft, $accounts, 10, 10),
                        '11' => $this->parseTrialBalanceSheet($saft, $accounts, 11, 11),
                        '12' => $this->parseTrialBalanceSheet($saft, $accounts, 12, 12),
                    ],
                ],
                'profit_loss_statement' => [
                    'all_year' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 1, 12),
                    'trimester' => [
                        '1' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 1, 3),
                        '2' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 4, 6),
                        '3' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 7, 9),
                        '4' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 10, 12),
                    ],
                    'month' => [
                        '1' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 1, 1),
                        '2' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 2, 2),
                        '3' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 3, 3),
                        '4' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 4, 4),
                        '5' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 5, 5),
                        '6' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 6, 6),
                        '7' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 7, 7),
                        '8' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 8, 8),
                        '9' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 9, 9),
                        '10' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 10, 10),
                        '11' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 11, 11),
                        '12' => $this->parseProfitLossStatementTaxonomies($saft, $accounts, 12, 12),
                    ],
                    
                    
                ],
                'balance_sheet' => [
                    'all_year' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 1, 12),
                    'month' => [
                        '1' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 1, 1),
                        '2' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 2, 2),
                        '3' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 3, 3),
                        '4' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 4, 4),
                        '5' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 5, 5),
                        '6' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 6, 6),
                        '7' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 7, 7),
                        '8' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 8, 8),
                        '9' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 9, 9),
                        '10' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 10, 10),
                        '11' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 11, 11),
                        '12' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 12, 12),
                    ],
                    'trimester' => [
                        '1' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 1, 3),
                        '2' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 4, 6),
                        '3' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 7, 9),
                        '4' => $this->parseBalanceSheetTaxonomies($saft, $accounts, 10, 12),
                    ],
                ]
            ];
        });
    }

    /**
     * Returns the company information from the saf-t.
     */
    protected function parseCompanyInformation(&$saft)
    {
        return [
            'company_name' => $saft['Header']['CompanyName'],
            'vat_number' => $saft['Header']['TaxRegistrationNumber'], // nif
            'address' => [
                'street_name' => $saft['Header']['CompanyAddress']['AddressDetail'],
                'postal_code' => $saft['Header']['CompanyAddress']['PostalCode'],
                'city' => $saft['Header']['CompanyAddress']['City'],
                'country' => $saft['Header']['CompanyAddress']['Country'],
            ],
            'fiscal_year' => $saft['Header']['FiscalYear'],
            'fiscal_year_start_date' => Carbon::parse($saft['Header']['StartDate'])->format('d/m/Y'),
            'fiscal_year_end_date' => Carbon::parse($saft['Header']['EndDate'])->format('d/m/Y'),
        ];
    }

    /**
     * Returns the company's trial balance sheet from the saf-t.
     */
    protected function parseTrialBalanceSheet(&$saft, $accounts, $start_month = null, $end_month = null)
    {
        //Filter out accounts that are not 'GR - Conta de 1.º grau da contabilidade geral.'
        $accounts = array_filter($accounts, function ($account) {
            return $account['category'] === 'GR';
        });

        // Remove transactions that don't belong to the specified time period.
        foreach($accounts as &$account) {
            $account['transactions'] = array_filter($account['transactions'], function ($transaction) use ($start_month, $end_month) {
                if($start_month != null && $transaction['date']->month < $start_month) return false;
                if($end_month != null && $transaction['date']->month > $end_month) return false;
                return true;
            });
            $debit_transactions = SaftService::get_sum_of_transactions($account, 'debit', $start_month, $end_month);
            $credit_transactions = SaftService::get_sum_of_transactions($account, 'credit', $start_month, $end_month);
            $account['balance'] = $debit_transactions - $credit_transactions;
            $account['end_debit'] = $account['opening_debit'] + $debit_transactions;
            $account['end_credit'] = $account['opening_credit'] + $credit_transactions;
            $account['debit_transactions'] = $debit_transactions;
            $account['credit_transactions'] = $credit_transactions;
        }

        return [
            'total_beginning' => [
                'debit' => array_reduce($accounts, function ($carry, $account) {
                    return $carry + $account['opening_debit'];
                }, 0),
                'credit' => array_reduce($accounts, function ($carry, $account) {
                    return $carry + $account['opening_credit'];
                }, 0),
            ],
            'total_transactions' => [
                'debit' => array_reduce($accounts, function ($carry, $account) use ($start_month, $end_month) {
                    return $carry + $account['debit_transactions'];
                }, 0),
                'credit' => array_reduce($accounts, function ($carry, $account) use ($start_month, $end_month) {
                    return $carry + $account['credit_transactions'] ;
                }, 0),
            ],
            'total_ending' => [
                'debit' => array_reduce($accounts, function ($carry, $account) {
                    return $carry + $account['end_debit'];
                }, 0),
                'credit' => array_reduce($accounts, function ($carry, $account) {
                    return $carry + $account['end_credit'];
                }, 0),
            ],
            'accounts' => $accounts
        ];
    }

    /**
     * Returns the company's accounts associated with transactions from the saf-t.
     */
    protected function parseAccounts(&$saft)
    {
        $accounts = $saft['MasterFiles']['GeneralLedgerAccounts']['Account'];

        // Format accounts in order to remove junk attributes.
        $accounts = array_map(function ($account) {
            return [
                'account_id' =>  $account['AccountID'],
                'description' =>  $account['AccountDescription'], // will be edited later
                'category' =>  $account['GroupingCategory'],
                'opening_credit'   => floatval($account['OpeningCreditBalance']),
                'opening_debit'   => floatval($account['OpeningDebitBalance']),
                'transactions' => [],
                'taxonomy_code' => intval($account['TaxonomyCode'] ?? null),
                'end_debit' => 0,
                'end_credit' => 0,
                'credit' => 0
            ];
        }, $accounts);

        // Fix account names.
        $account_names = [
            '11' => 'Caixa',
            '12' => 'Depósitos à ordem',
            '13' => 'Outros depósitos bancários',
            '14' => 'Outros instrumentos financeiros',
            '21' => 'Clientes',
            '22' => 'Fornecedores',
            '23' => 'Pessoal',
            '24' => 'Estado e outros entes públicos',
            '25' => 'Financiamentos obtidos',
            '26' => 'Acionistas/sócios',
            '27' => 'Outras contas a receber e a pagar',
            '28' => 'Diferimentos',
            '29' => 'Provisões',
            '31' => 'Compras',
            '32' => 'Mercadorias',
            '33' => 'Matérias-primas, subsidiárias e de consumo',
            '34' => 'Produtos acabados e intermédios',
            '35' => 'Subprodutos, desperdícios e refugos',
            '36' => 'Produtos e trabalhos em curso',
            '37' => 'Ativos biológicos',
            '38' => 'Reclassificação e regularização de inventários e ativos biológicos',
            '39' => 'Adiantamentos por conta de compras',
            '41' => 'Investimentos financeiros',
            '42' => 'Propriedades de investimento',
            '43' => 'Ativos fixos tangíveis',
            '44' => 'Ativos intangíveis',
            '45' => 'Investimentos em curso',
            '46' => 'Ativos não correntes detidos para venda',
            '51' => 'Capital',
            '52' => 'Ações (quotas) próprias',
            '53' => 'Outros instrumentos de capital próprio',
            '54' => 'Prémios de emissão',
            '55' => 'Reservas',
            '56' => 'Resultados transitados',
            '57' => 'Ajustamentos em ativos financeiros',
            '58' => 'Excedentes de revalorização de ativos fixos tangíveis e intangíveis',
            '59' => 'Outras variações no capital próprio',
            '61' => 'Custo das mercadorias vendidas e das matérias consumidas',
            '62' => 'Fornecimentos e serviços externos',
            '63' => 'Gastos com o pessoal',
            '64' => 'Gastos de depreciação e de amortização',
            '65' => 'Perdas por imparidade',
            '66' => 'Perdas por reduções de justo valor',
            '67' => 'Provisões do período',
            '68' => 'Outros gastos e perdas',
            '69' => 'Gastos e perdas de financiamento',
            '71' => 'Vendas',
            '72' => 'Prestações de serviços',
            '73' => 'Variações nos inventários de produção',
            '74' => 'Trabalhos para a própria entidade',
            '75' => 'Subsídios à exploração',
            '76' => 'Reversões',
            '77' => 'Ganhos por aumentos de justo valor',
            '78' => 'Outros rendimentos e ganhos',
            '79' => 'Juros, dividendos e outros rendimentos similares',
            '81' => 'Resultado liquído do período',
            '89' => 'Dividendos antecipados'
        ];
        foreach($accounts as &$account) { 
            if(array_key_exists($account['account_id'], $account_names)) {
                $account['description'] = $account_names[$account['account_id']];
            }
        }

        // Previous array does not have keys, only values. 
        // Add a key to each account; the key being the id of the account.
        $accounts = array_combine(array_map(function ($account) {
            return $account['account_id'];
        }, $accounts), $accounts);

        // Process transactions and associate them with the accounts.
        $journals = $saft['GeneralLedgerEntries']['Journal'];
        foreach ($journals as $journal) {
            $journal_id = $journal['JournalID'];
            $journal_description = $journal['Description'];

            if (!array_key_exists('Transaction', $journal))
                continue; // No transactions in this journal.

            // 1 Transaction.
            if (array_key_exists('TransactionID', $journal['Transaction']))
                $journal_transactions = [$journal['Transaction']];
            // Several transactions.
            else
                $journal_transactions = $journal['Transaction'];

            foreach ($journal_transactions as $transaction) {
                $transaction_period = $transaction['Period'];
                $transaction_date = Carbon::parse($transaction['TransactionDate']);
                $transaction_lines = $transaction['Lines'];

                // 1 credit line.
                if (array_key_exists('RecordID', $transaction_lines['CreditLine']))
                    $transaction_credit_lines = [$transaction_lines['CreditLine']];
                // Several credit lines.
                else
                    $transaction_credit_lines = $transaction_lines['CreditLine'];

                // 1 debit line.
                if (array_key_exists('RecordID', $transaction_lines['DebitLine']))
                    $transaction_debit_lines = [$transaction_lines['DebitLine']];
                // Several debit lines.
                else
                    $transaction_debit_lines = $transaction_lines['DebitLine'];

                // Link credit line to account.
                foreach ($transaction_credit_lines as $credit_line) {
                    $line_accountid = $credit_line['AccountID'];
                    $credit_amount = floatval($credit_line['CreditAmount']);
                    foreach ($accounts as &$account) {
                        if (substr($line_accountid, 0, strlen($account['account_id'])) === $account['account_id']) {
                            $account['transactions'][] = [
                                'type' => 'credit',
                                'amount' => floatval($credit_amount),
                                'date' => $transaction_date,
                                'period' => $transaction_period,
                                'is_N_type' => $transaction['TransactionType'] === "N"
                            ];
                        }
                    }
                }

                // Link debit line to account.
                foreach ($transaction_debit_lines as $debit_line) {
                    $line_accountid = $debit_line['AccountID'];
                    $debit_amount = floatval($debit_line['DebitAmount']);
                    foreach ($accounts as &$account) {
                        if (substr($line_accountid, 0, strlen($account['account_id'])) === $account['account_id']) {
                            $account['transactions'][] = [
                                'type' => 'debit',
                                'amount' => floatval($debit_amount),
                                'date' => $transaction_date,
                                'period' => $transaction_period,
                                'is_N_type' => $transaction['TransactionType'] === "N"
                            ];
                        }
                    }
                }
            }
        }

        // Calculate end_credit and end_balance on the accounts from the sum of the initial plus the transactions.
        foreach($accounts as &$account) {

            $account['end_debit'] = $account['opening_debit'] + SaftService::get_sum_of_transactions($account, 'debit');
            $account['end_credit'] = $account['opening_credit'] + SaftService::get_sum_of_transactions($account, 'credit');
            $account['balance'] = floatval($account['end_debit']) - floatval($account['end_credit']);
        }
        
        return $accounts;
    }

    /**
     * Parses the accounts retrieved from the parseAccounts method to be indexed by the taxonomy code.
     */
    protected function getAccountsByTaxonomy(&$accounts) {
        $return_array = [];
        foreach($accounts as &$account) {
            if($account['taxonomy_code'] == 0) continue; 
            if(!array_key_exists($account['taxonomy_code'], $return_array))
                $return_array[$account['taxonomy_code']] = [];
            $return_array[$account['taxonomy_code']][] = $account;
        }
        return $return_array;
    }

    /**
     * Returns the income's statement from the saf-t.
     */
    protected function parseProfitLossStatement(&$saft, $accounts, $start_month = null, $end_month = null)
    {
        // Filter out accounts that are not 'GR - Conta de 1.º grau da contabilidade geral.'
        $accounts = array_filter($accounts, function ($account) {
            return $account['category'] === 'GR';
        });

        // Grab "gastos" accounts.
        $expenses = array_filter($accounts, function ($account) {
            $first_digit = substr($account['account_id'], 0, 1);
            return $first_digit === '6';
        });

        // Calculate for the given time period
        foreach($expenses as &$expense) {
            $expense['transactions'] = array_filter($expense['transactions'], function ($transaction) use ($start_month, $end_month) {
                if($start_month != null && $transaction['date']->month < $start_month) return false;
                if($end_month != null && $transaction['date']->month > $end_month) return false;
                return true;
            });
            $expense['balance'] = SaftService::get_sum_of_transactions($expense, 'debit', $start_month, $end_month) - SaftService::get_sum_of_transactions($expense, 'credit', $start_month, $end_month);
        }

        $total_expenses = array_reduce($expenses, function ($carry, $account) {
            $carry -= $account['balance'];
            return $carry;
        }, 0);

        // Grab "rendimentos" accounts.
        $revenues = array_filter($accounts, function ($account) {
            $first_digit = substr($account['account_id'], 0, 1);
            return $first_digit === '7';
        });

        // Calculate for the given time period
        foreach($revenues as &$revenue) {
            $revenue['transactions'] = array_filter($revenue['transactions'], function ($transaction) use ($start_month, $end_month) {
                if($start_month != null && $transaction['date']->month < $start_month) return false;
                if($end_month != null && $transaction['date']->month > $end_month) return false;
                return true;
            });
            $revenue['balance'] = SaftService::get_sum_of_transactions($revenue, 'debit', $start_month, $end_month) - SaftService::get_sum_of_transactions($revenue, 'credit', $start_month, $end_month);
        }

        $total_revenues = array_reduce($revenues, function ($carry, $account) {
            $carry -= $account['balance'];
            return $carry;
        }, 0);

        $net_income = $total_revenues + $total_expenses;
        $ebit = $net_income;
        $ebitda = $net_income;

        return [
            'expenses' => $expenses,
            'revenues' => $revenues,
            'total_expenses' => $total_expenses,
            'total_revenues' => $total_revenues,
            'ebit' => $ebit,
            'ebitda' => $ebitda,
            'net_income' => $net_income,
        ];
    }

    /**
     * Returns the income's statement from the saf-t.
     */
    protected function parseProfitLossStatementTaxonomies(&$saft, &$accounts, $start_month = null, $end_month = null)
    {
        // +/- stupid cases...
        $doAbs = function($value)
        {
            return $value + 1000;
        };
        $taxonomies = $this->getAccountsByTaxonomy($accounts);
        $temp_statement = [
            "Vendas e serviços prestados" => 
            SaftService::get_income_account($taxonomies, [506, 507, 508, 509, $doAbs(510), 511, 512, 513, 514, 515, 516, $doAbs(517), 518], $start_month, $end_month),
            "Subsídios à exploração" =>                
            SaftService::get_income_account($taxonomies, [527, 528], $start_month, $end_month),
            "Ganhos / perdas imputados de subsidiárias, associadas e empreendimentos conjuntos" =>                
            SaftService::get_income_account($taxonomies, [614, 615, 616, 638, 639, -479, -480, -481, -482], $start_month, $end_month),
            "Variação nos inventários da produção" =>                
            SaftService::get_income_account($taxonomies, [$doAbs(519), $doAbs(520), $doAbs(521), $doAbs(522)], $start_month, $end_month),
            "Trabalhos para a própria entidade" =>                
            SaftService::get_income_account($taxonomies, [523, 524, 525, 526], $start_month, $end_month),
            "Custo das mercadorias vendidas e das matérias consumidas" =>                
            SaftService::get_income_account($taxonomies, [353, 354, 355], $start_month, $end_month),
            "Fornecimentos e serviços externos" =>
            SaftService::get_income_account($taxonomies, [356, 357, 358, 359, 360, 361, 362, 363, 364, 365, 366, 367, 368, 369, 370, 371, 372, 373, 374, 375, 376, 377, 378, 379, 380, 381, 382, 383, 384], $start_month, $end_month),
            "Gastos com o pessoal" =>
            SaftService::get_income_account($taxonomies, [385, 386, $doAbs(387), $doAbs(388), 389, 390, 391, 392, 393], $start_month, $end_month),
            "Imparidade / ajustamentos de inventários (perdas / reversões)" =>
            SaftService::get_income_account($taxonomies, [415, 416, 417, 418, 419, 420, 421, -549, -550,-551, -552, -553, -554, -555], $start_month, $end_month),
            "Imparidade de dívidas a receber (perdas / reversões)" =>
            SaftService::get_income_account($taxonomies, [413, 414, 547, 548], $start_month, $end_month),
            "Provisões (aumentos / reduções)" =>
            SaftService::get_income_account($taxonomies, [463, 464, 465, 466, 467, 468, 469, 470, -586, -587, -588, -589, -590, -591, -592, -593], $start_month, $end_month),
            "Imparidade de investimentos não depreciáveis / amortizáveis (perdas / reversões)" =>
            SaftService::get_income_account($taxonomies, [$doAbs(412), 422, 423, 424, 425, 441, 442, 443, 444, 445, 446, 447, 448, 449, 450, 451, 452, 453, -556, -557, -558, -573, -574, -575, -576, -577, -578, -579, -580, -581, -582, -583, -584, -585], $start_month, $end_month),
            "Aumentos / reduções de justo valor" =>
            SaftService::get_income_account($taxonomies, [594, 595, 596, 597, 598, 599, 600, 601, 602, -454, -455, -456, -457, -458, -459, -460, -461, -462], $start_month, $end_month),
            "Outros rendimentos" =>
            SaftService::get_income_account($taxonomies, [603, 604, 605, 606, 607, 608, 609, 610, 611, 612, 613, 617, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627, 628, 629, 630, 631, 632, 633, 634, 636, 637, 640, 642], $start_month, $end_month),
            "Outros gastos" =>
            // Resultado antes de depreciações, gastos de financiamento e impostos
            SaftService::get_income_account($taxonomies, [471, 472, 473, 474, 475, 476, 477, 478, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 496, 497, 498, 499], $start_month, $end_month),
            "Gastos / reversões de depreciação e de amortização" =>
            SaftService::get_income_account($taxonomies, [394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, -529, -530, -531, -532, -533, -534, -535, -536, -537, -538, -539, -540, -541, -542, -543, -544, -545, -546], $start_month, $end_month),  
            "Imparidade de investimentos depreciáveis / amortizáveis (perdas / reversões)" =>
            SaftService::get_income_account($taxonomies, [426, 427, 428, 429, 430, 431, 432, 433, 434, 435, 436, 437, 438, 439, 440, -559, -560, -561, -562, -563, -564, -565, -566, -567, -568, -569, -570, -571, -572], $start_month, $end_month),  
            // Resultado operacional (antes de gastos de financiamento e impostos)
            "Juros e rendimentos similares obtidos" =>
            SaftService::get_income_account($taxonomies, [635, 641], $start_month, $end_month),  
            "Juros e gastos similares suportados" =>
            SaftService::get_income_account($taxonomies, [500, 501, 502, 503, 504, 505], $start_month, $end_month),  
            // Resultado antes de impostos
            "Imposto sobre o rendimento do período" =>
            SaftService::get_income_account($taxonomies, [644, $doAbs(645)], $start_month, $end_month),  
        ];
        $statement = [];
        $statement['revenues'] = [
            'Vendas e serviços prestados' => $temp_statement['Vendas e serviços prestados'],
            'Subsídios à exploração' => $temp_statement['Subsídios à exploração'],
            'Ganhos / perdas imputados de subsidiárias, associadas e empreendimentos conjuntos' => $temp_statement['Ganhos / perdas imputados de subsidiárias, associadas e empreendimentos conjuntos'],
            'Variação nos inventários da produção' => $temp_statement['Variação nos inventários da produção'],
            'Trabalhos para a própria entidade' => $temp_statement['Trabalhos para a própria entidade'],
            'Aumentos / reduções de justo valor' => $temp_statement['Aumentos / reduções de justo valor'],
            'Outros rendimentos' => $temp_statement['Outros rendimentos'],
        ];
        $statement['expenses'] = [
            'Custo das mercadorias vendidas e das matérias consumidas' => $temp_statement['Custo das mercadorias vendidas e das matérias consumidas'],
            'Fornecimentos e serviços externos' => $temp_statement['Fornecimentos e serviços externos'],
            'Gastos com o pessoal' => $temp_statement['Gastos com o pessoal'],
            'Imparidade / ajustamentos de inventários (perdas / reversões)' => $temp_statement['Imparidade / ajustamentos de inventários (perdas / reversões)'],
            'Provisões (aumentos / reduções)' => $temp_statement['Provisões (aumentos / reduções)'],
            'Imparidade de dívidas a receber (perdas / reversões)' => $temp_statement['Imparidade de dívidas a receber (perdas / reversões)'],
            'Imparidade de investimentos não depreciáveis / amortizáveis (perdas / reversões)' => $temp_statement['Imparidade de investimentos não depreciáveis / amortizáveis (perdas / reversões)'],
            'Outros gastos' => $temp_statement['Outros gastos'],
        ];
        $statement['ebitda'] = array_sum($statement['revenues']) + array_sum($statement['expenses']);
        $statement['ebit'] = $statement['ebitda'] + $temp_statement['Gastos / reversões de depreciação e de amortização'] + $temp_statement['Imparidade de investimentos depreciáveis / amortizáveis (perdas / reversões)'];
        //$statement['ebit'] = $statement['operating_income'] + $temp_statement['Juros e rendimentos similares obtidos'] + $temp_statement['Juros e gastos similares suportados'];
        $statement['net_income'] = $statement['ebit'] + $temp_statement['Imposto sobre o rendimento do período'] + $temp_statement['Juros e rendimentos similares obtidos'] + $temp_statement['Juros e gastos similares suportados'];
        $statement['total_revenues'] = array_reduce($statement['revenues'], function ($carry, $amount) {
            $carry += $amount;
            return $carry;
        }, 0);
        $statement['total_expenses'] = array_reduce($statement['expenses'], function ($carry, $amount) {
            $carry += $amount;
            return $carry;
        }, 0);
        $statement['depreciation_amortization'] = $temp_statement['Gastos / reversões de depreciação e de amortização'] + $temp_statement['Imparidade de investimentos depreciáveis / amortizáveis (perdas / reversões)'];
        $statement['interest_taxes'] = $temp_statement['Juros e rendimentos similares obtidos'] + $temp_statement['Juros e gastos similares suportados'] + $temp_statement['Imposto sobre o rendimento do período'];

        return $statement;
    }

    /**
     * Returns the balance sheet from the saf-t.
     */
    protected function parseBalanceSheet(&$saft, $accounts, $start_month = null, $end_month = null)
    {
        // Filter out accounts that have more than 4 digits.
        $accounts = array_filter($accounts, function ($account) {
            return strlen($account['account_id']) <= 4;
        });

        // Initial unpopulated data structure.
        $balance_sheet = [
            'Ativo' => [
                'Ativo não corrente' => [
                    'Ativos fixos tangíveis' =>
                    SaftService::get_accounts_balance([$accounts[43] ?? null, $accounts[453] ?? null, $accounts[455] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[459] ?? null, $start_month, $end_month),
                    'Propriedades de investimento' =>
                    SaftService::get_accounts_balance([$accounts[42] ?? null, $accounts[455] ?? null, $accounts[452] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[459] ?? null, $start_month, $end_month),
                    'Ativos intangíveis' =>
                    SaftService::get_accounts_balance([$accounts[44] ?? null, $accounts[454] ?? null, $accounts[455] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[459] ?? null, $start_month, $end_month),
                    'Investimentos financeiros' =>
                    SaftService::get_accounts_balance($accounts[41] ?? null, $start_month, $end_month),
                    'Acionistas/Sócios' =>
                    SaftService::get_accounts_balance([$accounts[266] ?? null, $accounts[268] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[269] ?? null, $start_month, $end_month),
                ],
                'Ativo corrente' => [
                    'Inventários' =>
                    SaftService::get_accounts_balance([$accounts[32] ?? null, $accounts[33] ?? null, $accounts[34] ?? null, $accounts[35] ?? null, $accounts[36] ?? null, $accounts[39] ?? null], $start_month, $end_month),
                    'Clientes' =>
                    SaftService::get_accounts_balance([$accounts[211] ?? null, $accounts[212] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[219] ?? null, $start_month, $end_month),
                    'Adiantamentos a fornecedores' =>
                    SaftService::get_accounts_balance([$accounts[228] ?? null, $accounts[2713] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance([$accounts[229] ?? null, $accounts[279] ?? null], $start_month, $end_month),
                    'Estado e outros entes públicos' => 0,
                    'Acionistas/Sócios' =>
                    SaftService::get_accounts_balance([$accounts[263] ?? null, $accounts[268] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance($accounts[269] ?? null, $start_month, $end_month),
                    'Outras Contas a Receber' =>
                    SaftService::get_accounts_balance([$accounts[232] ?? null, $accounts[238] ?? null, $accounts[2721] ?? null, $accounts[278] ?? null], $start_month, $end_month) - SaftService::get_accounts_balance([$accounts[279] ?? null, $accounts[239] ?? null], $start_month, $end_month),
                    'Diferimentos' =>
                    SaftService::get_accounts_balance($accounts[281] ?? null, $start_month, $end_month),
                    'Outros ativos financeiros' =>
                    SaftService::get_accounts_balance($accounts[14] ?? null, $start_month, $end_month),
                    'Caixa e depósitos bancários' =>
                    SaftService::get_accounts_balance([$accounts[11] ?? null, $accounts[12] ?? null, $accounts[13] ?? null], $start_month, $end_month),
                ],
                'Total do Ativo' => 0,
            ],
            'Capital Próprio e Passivo' => [
                'Capital Próprio' => [
                    'Capital Realizado' =>
                    SaftService::get_accounts_balance($accounts[51] ?? null, $start_month, $end_month) - SaftService::get_accounts_balance([$accounts[261] ?? null, $accounts[262] ?? null], $start_month, $end_month),
                    'Acções (quotas) próprias' =>
                    SaftService::get_accounts_balance($accounts[52] ?? null, $start_month, $end_month),
                    'Outros instrumentos de capital próprio' =>
                    SaftService::get_accounts_balance($accounts[53] ?? null, $start_month, $end_month),
                    'Prémios de emissão' =>
                    SaftService::get_accounts_balance($accounts[54] ?? null, $start_month, $end_month),
                    'Reservas legais' =>
                    SaftService::get_accounts_balance($accounts[551] ?? null, $start_month, $end_month),
                    'Outras reservas' =>
                    SaftService::get_accounts_balance($accounts[552] ?? null, $start_month, $end_month),
                    'Resultados transitados' =>
                    SaftService::get_accounts_balance($accounts[56] ?? null, $start_month, $end_month),
                    'Excedentes de revalorização' =>
                    SaftService::get_accounts_balance($accounts[58] ?? null, $start_month, $end_month),
                    'Outras variações no capital próprio' =>
                    SaftService::get_accounts_balance($accounts[59] ?? null, $start_month, $end_month),
                    'Resultado líquido do período' =>
                    SaftService::get_accounts_balance($accounts[818] ?? null, $start_month, $end_month),
                ],
                'Passivo' => [
                    'Passivo não corrente' => [
                        'Provisões' =>
                        SaftService::get_accounts_balance($accounts[29] ?? null, $start_month, $end_month),
                        'Financiamentos obtidos' =>
                        SaftService::get_accounts_balance($accounts[25] ?? null, $start_month, $end_month),
                        'Outras contas a pagar' =>
                        SaftService::get_accounts_balance([$accounts[237] ?? null, $accounts[2711] ?? null, $accounts[2712] ?? null, $accounts[275] ?? null], $start_month, $end_month),
                    ],
                    'Passivo corrente' => [
                        'Fornecedores' =>
                        SaftService::get_accounts_balance([$accounts[221] ?? null, $accounts[222] ?? null, $accounts[225] ?? null], $start_month, $end_month),
                        'Adiantamentos de clientes' =>
                        SaftService::get_accounts_balance([$accounts[218] ?? null, $accounts[276] ?? null], $start_month, $end_month),
                        'Estado e outros entes públicos' => 0,
                        'Acionistas/Sócios' =>
                        SaftService::get_accounts_balance([$accounts[264] ?? null, $accounts[265] ?? null], $start_month, $end_month),
                        'Financiamentos obtidos' =>
                        SaftService::get_accounts_balance($accounts[25] ?? null, $start_month, $end_month),
                        'Outras contas a pagar' =>
                        SaftService::get_accounts_balance([$accounts[231] ?? null, $accounts[238] ?? null, $accounts[2711] ?? null, $accounts[2712] ?? null, $accounts[2722] ?? null, $accounts[278] ?? null], $start_month, $end_month),
                        'Diferimentos' =>
                        SaftService::get_accounts_balance([$accounts[282] ?? null, $accounts[283] ?? null], $start_month, $end_month),
                        'Outros passivos financeiros' => 0,
                    ],
                ],
                'Total do Capital Próprio' => 0,
                'Total do Passivo' => 0,
                'Total do Capital Próprio e do Passivo' => 0
            ]
        ];

        // Handle special cases
        if($accounts[14] ?? null) {
            $balance = SaftService::get_accounts_balance($accounts[14], $start_month, $end_month);
            if($balance > 0)
                $balance_sheet['Ativo']['Ativo corrente']['Outros ativos financeiros'] += $balance;
            else
                $balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Outros passivos financeiros'] += $balance;
        }
        if($accounts[24] ?? null) {
            $balance = SaftService::get_accounts_balance($accounts[24], $start_month, $end_month);
            if($balance > 0)
                $balance_sheet['Ativo']['Ativo corrente']['Estado e outros entes públicos'] += $balance;
            else
                $balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Estado e outros entes públicos'] += $balance;
        }
        if($accounts[266] ?? null) {
            $balance = SaftService::get_accounts_balance($accounts[266], $start_month, $end_month);
            if($balance > 0)
                $balance_sheet['Ativo']['Ativo corrente']['Acionistas/Sócios'] += $balance;
            else
                $balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Acionistas/Sócios'] += $balance;
        }
        if($accounts[268] ?? null) {
            $balance = SaftService::get_accounts_balance($accounts[268], $start_month, $end_month);
            if($balance > 0)
                $balance_sheet['Ativo']['Ativo corrente']['Acionistas/Sócios'] += $balance;
            else
                $balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Acionistas/Sócios'] += $balance;
        }
        if($accounts[278] ?? null) {
            $balance = SaftService::get_accounts_balance($accounts[278], $start_month, $end_month);
            if($balance > 0)
                $balance_sheet['Ativo']['Ativo corrente']['Outras Contas a Receber'] += $balance;
            else
                $balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']['Outras contas a pagar'] += $balance;
        }

        $balance_sheet['Ativo']['Total do Ativo'] = array_sum($balance_sheet['Ativo']['Ativo não corrente']) + array_sum($balance_sheet['Ativo']['Ativo corrente']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Passivo'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo não corrente']) + array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Capital Próprio']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio e do Passivo'] = $balance_sheet['Capital Próprio e Passivo']['Total do Passivo'] + $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio'];

        // Convert all values to positive.
        array_walk_recursive($balance_sheet, function(&$item) {
            $item = abs($item);
        });

        return $balance_sheet;
    }

    /**
     * Returns the balance sheet from the saf-t.
     */
    protected function parseBalanceSheetTaxonomies(&$saft, $accounts, $start_month = null, $end_month = null)
    {
        // +/- stupid cases...
        $doAbs = function($value)
        {
            return $value + 1000;
        };
        $taxonomies = $this->getAccountsByTaxonomy($accounts);

        $balance_sheet = [
            "Ativo" => [
                "Ativo não corrente" => [
                    "Ativos fixos tangíveis" =>
                    SaftService::get_taxonomy_balance($taxonomies, [268, 269, 270, 271, 272, 273, 274, 275, 276, 277, 278, 279, 280, 281, 282, 283, 284, 285, 286, 287, 288, 306, 310, 314, 318], $start_month, $end_month),
                    "Propriedades de investimento" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [259, 260, 261, 262, 263, 264, 265, 266, 267, 305, 309, 313, 317], $start_month, $end_month),
                    "Goodwill" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [217, 222, 227, 236, 237, 238, 240, 245, 250, 289, 294, 299], $start_month, $end_month),
                    "Ativos intangíveis" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [290, 291, 292, 293, 295, 296, 297, 298, 300, 301, 302, 303, 307, 311, 315, 319], $start_month, $end_month),
                    "Ativos biológicos" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [197, 198, 200, 202, 215], $start_month, $end_month),
                    "Participações financeiras  método da equivalência patrimonial" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [216, 221, 226, 239, 244, 249], $start_month, $end_month),
                    "Outros investimentos financeiros" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [218, 219, 220, 223, 224, 225, 228, 229, 230, 231, 232, 233, 234, 235, 241, 242, 243, 246, 247, 248, 251, 252, 253, 254, 255, 256, 257, 258, 304, 308, 312, 316], $start_month, $end_month),
                    "Créditos a receber" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [68, 70, 112, 121, 123, 129, 141, 145], $start_month, $end_month)
                    + SaftService::get_taxonomy_balance_devedor($taxonomies, [62, 64, 114, 125, 127, 139]),
                    "Ativos por impostos diferidos" =>                   
                    SaftService::get_taxonomy_balance($taxonomies, [133, 143], $start_month, $end_month),
                ],
                "Ativo corrente" => [
                    "Inventários" =>
                    SaftService::get_taxonomy_balance($taxonomies, [165, 166, 167, 168, 169, 170, 171, 172, 173, 174, 175, 176, 177, 178, 179, 180, 181, 182, 183, 184, 185, 186, 187, 188, 189, 190, 191, 192, 193, 194, 209, 210, 211, 212, 213], $start_month, $end_month),
                    "Ativos biológicos" =>
                    SaftService::get_taxonomy_balance($taxonomies, [195, 196, 199, 201, 214], $start_month, $end_month),
                    "Clientes" =>
                    SaftService::get_taxonomy_balance($taxonomies, [24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36 ], $start_month, $end_month)
                    + SaftService::get_taxonomy_balance_devedor($taxonomies, [10, 11, 12, 13, 14, 15, 16, 17,  18, 19, 20, 21, 22], $start_month, $end_month),
                    "Estado e outros entes públicos" =>
                    SaftService::get_taxonomy_balance($taxonomies, [73, 74, 79, 80], $start_month, $end_month)
                    + SaftService::get_taxonomy_balance_devedor($taxonomies, [71, 76, 77, 81, 82, 83, 84, 85], $start_month, $end_month),
                    "Capital subscrito e não realizado" =>
                    SaftService::get_taxonomy_balance($taxonomies, [106, 107, 115, 116], $start_month, $end_month),
                    "Outros créditos a receber" =>
                    SaftService::get_taxonomy_balance($taxonomies, [51, 52, 55, 56, 65, 66, 67, 69, 108, 111, 117, 118, 119, 120, 122, 128, 130, 140, 142, 144], $start_month, $end_month)
                    + SaftService::get_taxonomy_balance_devedor($taxonomies, [37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 61, 63, 109, 110, 113, 124, 126, 138], $start_month, $end_month),
                    "Diferimentos" =>
                    SaftService::get_taxonomy_balance($taxonomies, [146], $start_month, $end_month),
                    "Ativos financeiros detidos para negociação" =>
                    SaftService::get_taxonomy_balance($taxonomies, [4, 6], $start_month, $end_month),
                    "Outros ativos financeiros" =>
                    SaftService::get_taxonomy_balance($taxonomies, [8], $start_month, $end_month),
                    "Ativos não correntes detidos para venda" =>
                    SaftService::get_taxonomy_balance($taxonomies, [320, 321, 322, 323, 324, 326, 327, 328, 329, 330], $start_month, $end_month),
                    "Caixa e depósitos bancários" =>
                    SaftService::get_taxonomy_balance($taxonomies, [1], $start_month, $end_month)
                    + SaftService::get_taxonomy_balance_devedor($taxonomies, [2, 3], $start_month, $end_month),


                ]
            ],
            "Capital Próprio e Passivo" => [
                "Capital Próprio" => [
                    "Capital subscrito" =>
                    SaftService::get_taxonomy_balance($taxonomies, [331], $start_month, $end_month),
                    "Ações (quotas) próprias" =>
                    SaftService::get_taxonomy_balance($taxonomies, [332, $doAbs(333)], $start_month, $end_month),
                    "Outros instrumentos de capital próprio" =>
                    SaftService::get_taxonomy_balance($taxonomies, [334], $start_month, $end_month),
                    "Prémios de emissão" =>
                    SaftService::get_taxonomy_balance($taxonomies, [335], $start_month, $end_month),
                    "Reservas legais" =>
                    SaftService::get_taxonomy_balance($taxonomies, [336], $start_month, $end_month),
                    "Outras reservas" =>
                    SaftService::get_taxonomy_balance($taxonomies, [337], $start_month, $end_month),
                    "Resultados transitados" =>
                    SaftService::get_taxonomy_balance($taxonomies, [$doAbs(338)], $start_month, $end_month),
                    "Excedentes de revalorização" =>
                    SaftService::get_taxonomy_balance($taxonomies, [343, 344, 345, 346], $start_month, $end_month),
                    "Ajustamentos / outras variações no capital próprio" =>
                    SaftService::get_taxonomy_balance($taxonomies, [$doAbs(339), 340, $doAbs(341), $doAbs(342), $doAbs(347), $doAbs(348), 349, 350, 351, $doAbs(352)], $start_month, $end_month),
                    "Resultado líquido do período" =>
                    SaftService::get_taxonomy_balance($taxonomies, [$doAbs(646)], $start_month, $end_month),
                    "Dividendos antecipados" =>
                    SaftService::get_taxonomy_balance($taxonomies, [647], $start_month, $end_month),
                ],
                "Passivo" => [
                    "Passivo não corrente" => [
                        "Provisões" =>
                        SaftService::get_taxonomy_balance($taxonomies, [148, 149, 150, 151, 152, 153, 154, 155], $start_month, $end_month),
                        "Financiamentos obtidos" =>
                        SaftService::get_taxonomy_balance($taxonomies, [87, 89, 91, 93, 95, 97, 99, 101, 103, 105], $start_month, $end_month),
                        "Responsabilidades por benefícios pósemprego" =>
                        SaftService::get_taxonomy_balance($taxonomies, [132], $start_month, $end_month),
                        "Passivos por impostos diferidos" =>
                        SaftService::get_taxonomy_balance($taxonomies, [134], $start_month, $end_month),
                        "Outras dívidas a pagar" =>
                        SaftService::get_taxonomy_balance($taxonomies, [58, 60, 136], $start_month, $end_month)                    
                        + SaftService::get_taxonomy_balance_credor($taxonomies, [62, 64, 114, 125, 127], $start_month, $end_month),
                    ],
                    "Passivo corrente" => [
                        "Fornecedores" =>
                        SaftService::get_taxonomy_balance_credor($taxonomies, [37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50], $start_month, $end_month),
                        "Adiantamentos de clientes" =>
                        SaftService::get_taxonomy_balance($taxonomies, [23, 137], $start_month, $end_month)
                        + SaftService::get_taxonomy_balance_credor($taxonomies, [10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22], $start_month, $end_month),
                        "Estado e outros entes públicos" =>
                        SaftService::get_taxonomy_balance($taxonomies, [72, 75, 78], $start_month, $end_month)
                        + SaftService::get_taxonomy_balance_credor($taxonomies, [71, 76, 77, 81, 82, 83, 84, 85], $start_month, $end_month),
                        "Financiamentos obtidos" =>
                        SaftService::get_taxonomy_balance($taxonomies, [86, 88, 90, 92, 94, 96, 98, 100, 102, 104], $start_month, $end_month)
                        + SaftService::get_taxonomy_balance_credor($taxonomies, [2, 3], $start_month, $end_month),
                        "Outras dívidas a pagar" =>
                        SaftService::get_taxonomy_balance($taxonomies, [53, 54, 57, 59, 131, 135], $start_month, $end_month)
                        + SaftService::get_taxonomy_balance_credor($taxonomies, [61, 63, 109, 110, 113, 124, 126, 138], $start_month, $end_month),
                        "Diferimentos" =>
                        SaftService::get_taxonomy_balance($taxonomies, [147], $start_month, $end_month),
                        "Passivos financeiros detidos para negociação" =>
                        SaftService::get_taxonomy_balance($taxonomies, [5, 7], $start_month, $end_month),
                        "Outros passivos financeiros" =>
                        SaftService::get_taxonomy_balance($taxonomies, [9], $start_month, $end_month),
                        "Passivos não correntes detidos para venda" =>
                        SaftService::get_taxonomy_balance($taxonomies, [325], $start_month, $end_month),
                    ]
                ]
            ]
            // "Vendas e serviços prestados" => 
            // SaftService::get_income_account($taxonomies, [506, 507, 508, 509, $doAbs(510), -511, -512, 513, 514, 515, 516, $doAbs(517),-518], $start_month, $end_month),
        ];

        $balance_sheet['Ativo']['Total do Ativo corrente'] = array_sum($balance_sheet['Ativo']['Ativo corrente']);
        $balance_sheet['Ativo']['Total do Ativo não corrente'] = array_sum($balance_sheet['Ativo']['Ativo não corrente']);
        $balance_sheet['Ativo']['Total do Ativo'] = array_sum($balance_sheet['Ativo']['Ativo não corrente']) + array_sum($balance_sheet['Ativo']['Ativo corrente']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Passivo corrente'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Passivo não corrente'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo não corrente']);

        $balance_sheet['Capital Próprio e Passivo']['Total do Passivo'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo não corrente']) + array_sum($balance_sheet['Capital Próprio e Passivo']['Passivo']['Passivo corrente']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio'] = array_sum($balance_sheet['Capital Próprio e Passivo']['Capital Próprio']);
        $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio e do Passivo'] = $balance_sheet['Capital Próprio e Passivo']['Total do Passivo'] + $balance_sheet['Capital Próprio e Passivo']['Total do Capital Próprio'];

        // Convert all values to positive.
        array_walk_recursive($balance_sheet, function(&$item) {
            $item = abs($item);
        });

        return $balance_sheet;
    }


    /**
     * Returns the sum of transaction of a given type that affected a particular account.
     * $accounts Array representing one account (obtained from some of the other methods of this class) or an array of accounts.
     * $type 'debit' or 'credit'
     * $start_month the starting month to take into account (1-12)
     * $end_month the ending month to take into account (1-12)
     */
    public static function get_sum_of_transactions($accounts, $type, $start_month = null, $end_month = null, $max_period = null)
    {
        $sum = 0; 

        // If $accounts is not an array of accounts make it so!
        if ($accounts === null || array_key_exists('account_id', $accounts))
            $accounts = [$accounts];

        foreach ($accounts as $account) {
            if ($account === null) continue;

            foreach ($account['transactions'] as $transaction) {
                if($max_period != null && !$transaction['is_N_type'])
                    continue;
                if ($start_month != null && $transaction['date']->month < $start_month)
                    continue;
                if ($end_month != null && $transaction['date']->month > $end_month)
                    continue;
                if ($transaction['type'] === $type)
                    $sum += $transaction['amount'];
            }
        }
        return $sum;
    }

    /**
     * Returns the sum of the accounts balances.
     * $accounts Array representing one account (obtained from some of the other methods of this class) or an array of accounts.
     * $3 the starting month to take into account (1-12)
     * $end_month the ending month to take into account (1-12)
     */
    public static function get_accounts_balance($accounts, $start_month = null, $end_month = null)
    {
        if($accounts === null) return 0;

        if(array_key_exists('account_id', $accounts)) {
            $accounts = [$accounts]; // Wrap single account passed as $accounts parameter.
        }
        $opening_debit = array_reduce($accounts, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_debit'];
            }
        }, 0);
        $opening_credit = array_reduce($accounts, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_credit'];
            }
        }, 0);
        return 
            + SaftService::get_sum_of_transactions($accounts, 'debit', $start_month, $end_month) 
            + $opening_debit
            - SaftService::get_sum_of_transactions($accounts, 'credit', $start_month, $end_month)
            - $opening_credit;
    }

    /**
     * Returns the balance of some taxonomy accounts.
     * $all_taxonomies an associative array which associated the taxonomies integer with a array that has a collection of the affected accounts.
     * $taxonomies an array containing the taxonomies integers.
     * $start_month the starting month (1 to 12)
     * $end_month the ending month (1 to 12)
     */
    public static function get_taxonomy_balance(&$all_taxonomies, $taxonomies, $start_month = null, $end_month = null)
    { 
        $accounts_positive = [];
        $accounts_negative = [];
        $accounts_abs = [];
        foreach($taxonomies as $taxonomy) {
            $shouldAbs = false;
            $shouldNeg = false;
            if($taxonomy > 1000) {
                $taxonomy -= 1000;
                $shouldAbs = true;
            }
            if($taxonomy < 0)
                $shouldNeg = true;
            if(!array_key_exists(abs($taxonomy), $all_taxonomies))
                continue;
            foreach($all_taxonomies[abs($taxonomy)] as $account) {
                if($shouldAbs)
                    $accounts_abs[] = $account;
                else if($shouldNeg)
                    $accounts_negative[] = $account;
                else
                    $accounts_positive[] = $account;
            }
        }    

        $opening_debit_accounts_positive = array_reduce($accounts_positive, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_debit'];
            }
        }, 0);
        $opening_credit_accounts_positive = array_reduce($accounts_positive, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_credit'];
            }
        }, 0);
        $opening_debit_accounts_abs = array_reduce($accounts_abs, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_debit'];
            }
        }, 0);
        $opening_credit_accounts_abs = array_reduce($accounts_abs, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_credit'];
            }
        }, 0);
        $opening_debit_accounts_negative = array_reduce($accounts_negative, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_debit'];
            }
        }, 0);
        $opening_credit_accounts_negative = array_reduce($accounts_negative, function ($carry, $account) {
            if ($account === null) {
                return $carry;
            } else {
                return $carry + $account['opening_credit'];
            }
        }, 0);

        $pos =  
            + SaftService::get_sum_of_transactions($accounts_positive, 'debit', $start_month, $end_month) 
            + $opening_debit_accounts_positive
            - SaftService::get_sum_of_transactions($accounts_positive, 'credit', $start_month, $end_month)
            - $opening_credit_accounts_positive;
        $neg = -(
            + SaftService::get_sum_of_transactions($accounts_negative, 'debit', $start_month, $end_month) 
            + $opening_debit_accounts_negative
            - SaftService::get_sum_of_transactions($accounts_negative, 'credit', $start_month, $end_month)
            - $opening_credit_accounts_negative);
        $abs = (
            + SaftService::get_sum_of_transactions($accounts_abs, 'debit', $start_month, $end_month) 
            + $opening_debit_accounts_abs
            - SaftService::get_sum_of_transactions($accounts_abs, 'credit', $start_month, $end_month)
            - $opening_credit_accounts_abs);
        return $pos + $neg + $abs;
    }

    public static function get_taxonomy_balance_devedor(&$all_taxonomies, $taxonomies, $start_month = null, $end_month = null)
    {
        $sum = 0;
        foreach($taxonomies as $taxonomy) {
            $balance = SaftService::get_taxonomy_balance($all_taxonomies, [$taxonomy], $start_month, $end_month);
            if($balance > 0) $sum += $balance;
    
        }
        return $sum;
    }

    public static function get_taxonomy_balance_credor(&$all_taxonomies, $taxonomies, $start_month = null, $end_month = null)
    {
        $sum = 0;
        foreach($taxonomies as $taxonomy) {
            $balance = SaftService::get_taxonomy_balance($all_taxonomies, [$taxonomy], $start_month, $end_month);
            if($balance < 0) $sum += $balance;
    
        }
        return $sum;
    }

    public static function get_income_account(&$all_taxonomies, $taxonomies, $start_month = null, $end_month = null) {
        $accounts_positive = [];
        $accounts_negative = [];
        $accounts_abs = [];
        foreach($taxonomies as $taxonomy) {
            $shouldAbs = false;
            $shouldNeg = false;
            if($taxonomy > 1000) {
                $taxonomy -= 1000;
                $shouldAbs = true;
            }
            if($taxonomy < 0)
                $shouldNeg = true;
            if(!array_key_exists(abs($taxonomy), $all_taxonomies))
                continue;
            foreach($all_taxonomies[abs($taxonomy)] as $account) {
                if($shouldAbs)
                    $accounts_abs[] = $account;
                else if($shouldNeg)
                    $accounts_negative[] = $account;
                else
                    $accounts_positive[] = $account;
            }
        }    
        $pos = SaftService::get_sum_of_transactions($accounts_positive, 'credit', $start_month, $end_month, 13) - SaftService::get_sum_of_transactions($accounts_positive, 'debit', $start_month, $end_month, 13);
        $neg =  -(SaftService::get_sum_of_transactions($accounts_negative, 'credit', $start_month, $end_month, 13) - SaftService::get_sum_of_transactions($accounts_negative, 'debit', $start_month, $end_month, 13));
        $abs = (SaftService::get_sum_of_transactions($accounts_abs, 'credit', $start_month, $end_month, 13) - SaftService::get_sum_of_transactions($accounts_abs, 'debit', $start_month, $end_month, 13));
        return $pos + $neg + $abs;
    }

    /**
     * Returns a parsed section (named $what).
     * See the constructor for the possible sections you can get.
     */
    public function get($what)
    {
        return $this->data[$what];
    }
}
