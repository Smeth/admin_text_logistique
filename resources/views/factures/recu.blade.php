<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu - {{ $coli->numero_suivi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            background: #fff;
            padding: 20px;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            border: 2px solid #333;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .header h1 {
            font-size: 28px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .header h2 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .receipt-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f3f4f6;
        }
        .details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .detail-row strong {
            font-weight: bold;
        }
        .payments {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #333;
        }
        .payment-item {
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9fafb;
            border-left: 4px solid #1e40af;
        }
        .total {
            text-align: center;
            margin-top: 20px;
            padding: 15px;
            background-color: #1e40af;
            color: white;
            font-size: 18px;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            color: #666;
            font-size: 10px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body {
                padding: 0;
            }
            .receipt-container {
                border: 2px solid #333;
            }
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="header">
            <h1>LIVRANGO</h1>
            <h2>REÇU DE PAIEMENT</h2>
        </div>

        <div class="receipt-info">
            <p><strong>N° Reçu:</strong> REC-{{ $coli->numero_suivi }}</p>
            <p><strong>Date:</strong> {{ now()->format('d/m/Y à H:i') }}</p>
        </div>

        <div class="details">
            <div class="detail-row">
                <span><strong>Client:</strong></span>
                <span>{{ $coli->client->full_name }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Numéro de suivi:</strong></span>
                <span>{{ $coli->numero_suivi }}</span>
            </div>
            <div class="detail-row">
                <span><strong>Colis:</strong></span>
                <span>{{ $coli->poids }} kg - {{ $coli->agenceDepart->nom_agence }} → {{ $coli->agenceArrivee->nom_agence }}</span>
            </div>
        </div>

        @if($coli->paiements->count() > 0)
        <div class="payments">
            <h3 style="margin-bottom: 15px; color: #1e40af;">Détails des Paiements</h3>
            @foreach($coli->paiements as $paiement)
            <div class="payment-item">
                <div class="detail-row">
                    <span><strong>Date:</strong></span>
                    <span>{{ $paiement->date_paiement->format('d/m/Y H:i') }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Montant:</strong></span>
                    <span>{{ number_format($paiement->montant, 0, ',', ' ') }} {{ $paiement->devise ? $paiement->devise->symbole : 'FCFA' }}</span>
                </div>
                <div class="detail-row">
                    <span><strong>Mode:</strong></span>
                    <span>{{ ucfirst(str_replace('_', ' ', $paiement->mode_paiement)) }}</span>
                </div>
                @if($paiement->caisse)
                <div class="detail-row">
                    <span><strong>Caisse:</strong></span>
                    <span>{{ $paiement->caisse->nom_caisse }}</span>
                </div>
                @endif
                <div class="detail-row">
                    <span><strong>Enregistré par:</strong></span>
                    <span>{{ $paiement->user->name }}</span>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        <div class="total">
            <div>Montant Total Payé: {{ number_format($coli->total_paye, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</div>
            @if($coli->montant_restant > 0)
            <div style="margin-top: 10px; font-size: 14px;">Reste à payer: {{ number_format($coli->montant_restant, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</div>
            @else
            <div style="margin-top: 10px; font-size: 14px;">✓ Colis entièrement payé</div>
            @endif
        </div>

        <div class="footer">
            <p>Merci de votre confiance !</p>
            <p>Ce reçu est généré automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>

