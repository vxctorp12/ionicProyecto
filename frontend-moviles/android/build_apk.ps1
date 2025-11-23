$env:JAVA_HOME = 'C:\Program Files\Java\jdk-21'
$env:PATH = "$env:JAVA_HOME\bin;$env:PATH"
Write-Host "Starting Build..."
./gradlew assembleDebug --stacktrace > build_output.log 2>&1
