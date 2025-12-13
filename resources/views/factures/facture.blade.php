<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture - {{ $coli->numero_suivi }}</title>
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
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border: 1px solid #ddd;
            padding: 30px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #333;
        }
        .company-info h1 {
            font-size: 24px;
            color: #1e40af;
            margin-bottom: 10px;
        }
        .invoice-info {
            text-align: right;
        }
        .invoice-info h2 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }
        .details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            font-size: 14px;
            color: #1e40af;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        .section p {
            margin: 5px 0;
            line-height: 1.6;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table th, table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        table th {
            background-color: #f3f4f6;
            font-weight: bold;
            color: #333;
        }
        .total-row {
            font-weight: bold;
            font-size: 14px;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div class="company-info">
                <h1>LIVRANGO</h1>
                <p>Service de Transport de Colis</p>
                <p>Email: contact@livrango.com</p>
                <p>Téléphone: +237 XXX XXX XXX</p>
            </div>
            <div class="invoice-info">
                <h2>FACTURE</h2>
                <p><strong>N°:</strong> {{ $coli->numero_suivi }}</p>
                <p><strong>Date:</strong> {{ $coli->date_envoi->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="details">
            <div class="section">
                <h3>Informations Client</h3>
                <p><strong>Nom:</strong> {{ $coli->client->full_name }}</p>
                <p><strong>Email:</strong> {{ $coli->client->email ?? 'N/A' }}</p>
                <p><strong>Téléphone:</strong> {{ $coli->client->telephone ?? 'N/A' }}</p>
                @if($coli->client->adresse)
                <p><strong>Adresse:</strong> {{ $coli->client->adresse }}</p>
                @endif
            </div>
            <div class="section">
                <h3>Informations Colis</h3>
                <p><strong>Numéro de suivi:</strong> {{ $coli->numero_suivi }}</p>
                <p><strong>Poids:</strong> {{ $coli->poids }} kg</p>
                @if($coli->dimensions)
                <p><strong>Dimensions:</strong> {{ $coli->dimensions }}</p>
                @endif
                <p><strong>Agence Départ:</strong> {{ $coli->agenceDepart->nom_agence }}</p>
                <p><strong>Agence Arrivée:</strong> {{ $coli->agenceArrivee->nom_agence }}</p>
                @if($coli->pays_origine || $coli->ville_origine)
                <p><strong>Lieu d'origine:</strong> 
                    @if($coli->ville_origine && $coli->pays_origine)
                        {{ $coli->ville_origine }}, {{ $coli->pays_origine }}
                    @elseif($coli->ville_origine)
                        {{ $coli->ville_origine }}
                    @elseif($coli->pays_origine)
                        {{ $coli->pays_origine }}
                    @endif
                </p>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Quantité</th>
                    <th>Prix unitaire</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        Transport de colis<br>
                        <small>De {{ $coli->agenceDepart->nom_agence }} vers {{ $coli->agenceArrivee->nom_agence }}</small>
                        @if($coli->tarif)
                        <br><small>Tarif: {{ $coli->tarif->nom_tarif }}</small>
                        @endif
                    </td>
                    <td>1</td>
                    <td>{{ number_format($coli->frais_transport, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</td>
                    <td>{{ number_format($coli->frais_transport, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" style="text-align: right;">TOTAL TTC:</td>
                    <td>{{ number_format($coli->frais_transport, 0, ',', ' ') }} {{ $coli->devise ? $coli->devise->symbole : 'FCFA' }}</td>
                </tr>
            </tfoot>
        </table>

        @if($coli->description_contenu)
        <div class="section">
            <h3>Description du contenu</h3>
            <p>{{ $coli->description_contenu }}</p>
        </div>
        @endif

        <div class="footer">
            <p>Merci de votre confiance !</p>
            <p>Cette facture est générée automatiquement le {{ now()->format('d/m/Y à H:i') }}</p>
        </div>
    </div>
</body>
</html>

