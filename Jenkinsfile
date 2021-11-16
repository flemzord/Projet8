pipeline {
  agent any
  stages {
    stage('deps') {
      steps {
        sh 'composer install -n'
      }
    }
    stage('tests:unit') {
      steps {
        sh 'php -d date.timezone=UTC ./vendor/bin/phpunit -c tests/Unit/phpunit.xml'
      }
    }
    stage('build') {
      steps {
        sh 'docker-compose build'
      }
    }
    stage('launch') {
      steps {
        sh 'docker-compose up -d'
      }
    }
    stage('tests:functional') {
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
   post {
      always {
         sh "docker-compose down || true"
      }
   }   
}
