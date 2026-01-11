# Git 협업 & 자동 배포 학습 프로젝트

Git 협업 워크플로우와 GitHub Actions를 활용한 Docker 자동 배포 학습 프로젝트입니다.

## 🎯 프로젝트 목표

- Git 브랜치 전략 학습 (develop/master)
- Pull Request 기반 협업 프로세스
- GitHub Actions 자동 배포 파이프라인
- Docker 컨테이너 기반 배포

## 🏗️ 브랜치 전략

```
main/master (운영 서버)
    ↑
develop (개발 서버)
    ↑
feature/* (기능 개발)
```

## 🚀 배포 프로세스

### 개발 서버 배포
1. feature 브랜치에서 작업
2. develop 브랜치로 PR 생성
3. 리뷰 후 병합
4. **GitHub Actions 자동 실행** → 개발 서버 배포

### 운영 서버 배포
1. develop → master PR 생성
2. 최종 검토 후 병합
3. **GitHub Actions 자동 실행** → 운영 서버 배포

## 🐳 Docker 실행 방법

### 개발 환경
```bash
docker-compose up -d
# http://localhost:8080 접속
```

### 운영 환경
```bash
docker-compose -f docker-compose.prod.yml up -d
# http://localhost 접속
```

## 📚 학습 가이드

- [Git 워크플로우 완벽 가이드](./GIT_WORKFLOW_GUIDE.md)
- [5분 퀵스타트](./QUICK_START.md)
- [PR 테스트 문서](./PR-TEST.md)

## 🛠️ 기술 스택

- **언어**: PHP 8.2
- **웹서버**: Apache
- **컨테이너**: Docker & Docker Compose
- **CI/CD**: GitHub Actions
- **버전관리**: Git & GitHub

## 📖 워크플로우 예시

```bash
# 1. 작업 시작
git checkout develop
git pull origin develop
git checkout -b feature/new-feature

# 2. 작업 & 커밋
# ... 코드 작성 ...
git add .
git commit -m "feat: 새 기능 추가"

# 3. Push & PR
git push origin feature/new-feature
# GitHub에서 PR 생성 → develop에 병합

# 4. 자동 배포
# GitHub Actions가 자동으로 Docker 이미지 빌드 및 배포
```

## 🎓 학습한 내용

- ✅ Git 브랜치 전략
- ✅ Pull Request 생성 및 병합
- ✅ Squash Merge
- ✅ 브랜치 보호 규칙
- ✅ GitHub Actions 워크플로우
- ✅ Docker 이미지 빌드
- ✅ 자동 배포 파이프라인

## 📝 라이선스

MIT License - 학습용 프로젝트
