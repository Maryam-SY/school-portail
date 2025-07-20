<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin Disponible</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8f9fa;
            padding: 20px;
            border: 1px solid #dee2e6;
        }
        .footer {
            background-color: #6c757d;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 0 0 5px 5px;
            font-size: 12px;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            background-color: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎓 Portail Scolaire</h1>
        <h2>Nouveau Bulletin Disponible</h2>
    </div>
    
    <div class="content">
        <p>Bonjour <strong>{{ $eleve->prenom }} {{ $eleve->nom }}</strong>,</p>
        
        <p>Nous avons le plaisir de vous informer qu'un nouveau bulletin est disponible pour la période :</p>
        
        <div class="info">
            <h3>📊 Bulletin - {{ $periode }}</h3>
            <p><strong>Élève :</strong> {{ $eleve->prenom }} {{ $eleve->nom }}</p>
            <p><strong>Période :</strong> {{ $periode }}</p>
        </div>
        
        <p>Vous pouvez maintenant :</p>
        <ul>
            <li>Consulter votre bulletin en ligne</li>
            <li>Télécharger le PDF de votre bulletin</li>
            <li>Voir vos moyennes et votre rang dans la classe</li>
        </ul>
        
        <p style="text-align: center;">
            <a href="{{ url('/api/bulletins/' . $eleve->id . '/pdf/' . $periode) }}" class="button">
                📄 Télécharger le Bulletin PDF
            </a>
        </p>
        
        <p><strong>Important :</strong> Ce bulletin est confidentiel et destiné uniquement à vous et vos parents.</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Portail Scolaire - Tous droits réservés</p>
        <p>Cet email a été envoyé automatiquement, merci de ne pas y répondre.</p>
    </div>
</body>
</html> 