<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Bulletin de notes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .student-info {
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 5px;
        }
        .grades-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .grades-table th, .grades-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .grades-table th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 5px;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>BULLETIN DE NOTES</h1>
        <h2>Établissement Scolaire</h2>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td><strong>Élève :</strong></td>
                <td>{{ $bulletin['eleve']['prenom'] }} {{ $bulletin['eleve']['nom'] }}</td>
                <td><strong>Classe :</strong></td>
                <td>{{ $bulletin['classe'] }}</td>
            </tr>
            <tr>
                <td><strong>Période :</strong></td>
                <td>{{ $bulletin['periode'] }}</td>
                <td><strong>Rang :</strong></td>
                <td>{{ $bulletin['rang'] }}/{{ count($bulletin['notes']) }}</td>
            </tr>
        </table>
    </div>

    <table class="grades-table">
        <thead>
            <tr>
                <th>Matière</th>
                <th>Note</th>
                <th>Type d'évaluation</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bulletin['notes'] as $note)
            <tr>
                <td>{{ $note['matiere'] }}</td>
                <td>{{ $note['note'] }}/20</td>
                <td>{{ $note['type'] ?? 'Non spécifié' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Résumé</h3>
        <p><strong>Moyenne générale :</strong> {{ $bulletin['moyenne'] }}/20</p>
        <p><strong>Mention :</strong> {{ $bulletin['mention'] }}</p>
        <p><strong>Rang dans la classe :</strong> {{ $bulletin['rang'] }}</p>
    </div>

    <div class="footer">
        <p>Bulletin généré le {{ date('d/m/Y à H:i') }}</p>
        <p>Signature du professeur principal : _________________</p>
    </div>
</body>
</html> 