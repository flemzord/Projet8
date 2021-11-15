pipeline {
  agent { docker { image 'python:3.8.10' } }
  stages {
    stage('build') {
      steps {
        sh 'pip3 install -r requirements.txt'
      }
    }
    stage('test') {
      steps {
        sh 'python3 dftg.py'
      }
      post {
        always {
          junit 'test-reports/*.xml'
        }
      }    
    }
  }
}