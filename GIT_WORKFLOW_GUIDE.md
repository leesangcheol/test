# Git 협업 워크플로우 실습 가이드

## 📋 목차
1. [초기 설정](#초기-설정)
2. [브랜치 전략 구축](#브랜치-전략-구축)
3. [Pull Request 실습](#pull-request-실습)
4. [자동 배포 설정](#자동-배포-설정)
5. [전체 워크플로우 실습](#전체-워크플로우-실습)

---

## 1️⃣ 초기 설정

### Step 1: develop 브랜치 생성

```bash
# 현재 master 브랜치에서
git checkout -b develop
git push -u origin develop
```

### Step 2: GitHub에서 기본 브랜치 변경
1. GitHub 저장소 페이지 접속
2. `Settings` → `Branches`
3. Default branch를 `master`에서 `develop`으로 변경
4. `main` 브랜치는 보호 설정 (직접 push 금지)

### Step 3: 브랜치 보호 규칙 설정
**develop 브랜치 보호:**
1. `Settings` → `Branches` → `Add rule`
2. Branch name pattern: `develop`
3. 체크 항목:
   - ✅ Require pull request before merging
   - ✅ Require approvals (1명 이상)
4. Save changes

**main 브랜치 보호 (동일하게):**
- Branch name pattern: `main` (또는 `master`)

---

## 2️⃣ 브랜치 전략 구축

### 브랜치 구조
```
main (운영 서버)
  ↑
develop (개발 서버)
  ↑
feature/기능명 (개발자 작업)
```

### 브랜치 명명 규칙
- `feature/login` - 새 기능 개발
- `fix/bug-description` - 버그 수정
- `hotfix/critical-fix` - 긴급 수정
- `refactor/code-improvement` - 리팩토링

---

## 3️⃣ Pull Request 실습

### 시나리오: "로그인 기능 추가"

#### 👤 개발자A 역할 (현재 폴더)

**1. 작업 시작**
```bash
# develop 브랜치로 이동
git checkout develop

# 최신 상태로 업데이트
git pull origin develop

# 새 작업 브랜치 생성
git checkout -b feature/login
```

**2. 코드 작성**
```bash
# 예: login.php 파일 생성
echo "<?php\n// 로그인 기능\nfunction login() {\n    return 'Login Success';\n}\n?>" > login.php

# 변경사항 확인
git status
```

**3. 커밋**
```bash
git add .
git commit -m "feat: 로그인 기능 추가"
```

**4. GitHub에 푸시**
```bash
git push origin feature/login
```

**5. Pull Request 생성**

**방법 1: GitHub 웹사이트에서**
1. GitHub 저장소 페이지 접속
2. 노란색 배너 표시됨: "feature/login had recent pushes"
3. `Compare & pull request` 버튼 클릭
4. PR 작성:
   ```
   제목: feat: 로그인 기능 추가

   설명:
   ## 변경 사항
   - 로그인 함수 구현
   - login.php 파일 추가

   ## 테스트
   - [ ] 로그인 성공 케이스 확인
   - [ ] 에러 처리 확인
   ```
5. `Create pull request` 클릭

**방법 2: GitHub CLI 사용**
```bash
# gh CLI 설치 후
gh pr create --base develop --head feature/login --title "feat: 로그인 기능 추가" --body "로그인 기능을 추가했습니다."
```

**6. 리뷰 대기**
- 팀원(개발자B)이 코드 리뷰
- 수정 요청이 있으면 추가 커밋 후 push (자동으로 PR에 반영됨)

#### 👤 개발자B 역할 (다른 폴더)

**1. 코드 리뷰**
```bash
# 다른 폴더로 이동 (협업 연출용)
cd [다른_폴더_경로]

# PR 브랜치 가져오기
git fetch origin
git checkout feature/login

# 코드 확인
cat login.php
```

**2. GitHub에서 리뷰 작성**
1. Pull Requests 탭 → 해당 PR 클릭
2. `Files changed` 탭에서 코드 확인
3. 코드 라인 옆 `+` 버튼으로 코멘트 작성
4. `Review changes` → `Approve` → `Submit review`

**3. PR 병합**
1. `Merge pull request` 버튼 클릭
2. 병합 방식 선택:
   - `Squash and merge` (권장) - 여러 커밋을 1개로 합침
   - `Merge commit` - 모든 커밋 유지
3. `Confirm merge` 클릭
4. `Delete branch` 클릭 (GitHub에서 브랜치 삭제)

#### 👤 개발자A - 작업 마무리

```bash
# develop 브랜치로 돌아가기
git checkout develop

# 최신 develop 가져오기 (방금 병합된 내용 포함)
git pull origin develop

# 로컬 브랜치 삭제
git branch -d feature/login

# 원격 브랜치 삭제 (GitHub에서 이미 삭제했다면 불필요)
git push origin --delete feature/login
```

---

## 4️⃣ 자동 배포 설정 (GitHub Actions)

### Docker 이미지 빌드 & 배포 워크플로우

**`.github/workflows/deploy-dev.yml` 생성**

```yaml
name: Deploy to Development Server

on:
  push:
    branches:
      - develop

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest

    steps:
    - name: Checkout code
      uses: actions/checkout@v3

    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2

    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}

    - name: Build and push
      uses: docker/build-push-action@v4
      with:
        context: .
        push: true
        tags: your-dockerhub-username/your-app:dev-${{ github.sha }}

    - name: Deploy to Development Server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.DEV_SERVER_HOST }}
        username: ${{ secrets.DEV_SERVER_USER }}
        key: ${{ secrets.DEV_SERVER_SSH_KEY }}
        script: |
          docker pull your-dockerhub-username/your-app:dev-${{ github.sha }}
          docker stop your-app-dev || true
          docker rm your-app-dev || true
          docker run -d --name your-app-dev -p 8080:80 your-dockerhub-username/your-app:dev-${{ github.sha }}
```

**`.github/workflows/deploy-prod.yml` 생성**

```yaml
name: Deploy to Production Server

on:
  push:
    branches:
      - main

jobs:
  build-and-deploy:
    runs-on: ubuntu-latest

    steps:
    - name: Checkout code
      uses: actions/checkout@v3

    - name: Set up Docker Buildx
      uses: docker/setup-buildx-action@v2

    - name: Login to Docker Hub
      uses: docker/login-action@v2
      with:
        username: ${{ secrets.DOCKER_USERNAME }}
        password: ${{ secrets.DOCKER_PASSWORD }}

    - name: Build and push
      uses: docker/build-push-action@v4
      with:
        context: .
        push: true
        tags: your-dockerhub-username/your-app:latest

    - name: Deploy to Production Server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.PROD_SERVER_HOST }}
        username: ${{ secrets.PROD_SERVER_USER }}
        key: ${{ secrets.PROD_SERVER_SSH_KEY }}
        script: |
          docker pull your-dockerhub-username/your-app:latest
          docker stop your-app-prod || true
          docker rm your-app-prod || true
          docker run -d --name your-app-prod -p 80:80 your-dockerhub-username/your-app:latest
```

### GitHub Secrets 설정

1. GitHub 저장소 → `Settings` → `Secrets and variables` → `Actions`
2. `New repository secret` 클릭
3. 추가할 Secrets:
   - `DOCKER_USERNAME`: Docker Hub 사용자명
   - `DOCKER_PASSWORD`: Docker Hub 비밀번호
   - `DEV_SERVER_HOST`: 개발 서버 IP
   - `DEV_SERVER_USER`: 개발 서버 SSH 사용자
   - `DEV_SERVER_SSH_KEY`: 개발 서버 SSH 키
   - `PROD_SERVER_HOST`: 운영 서버 IP
   - `PROD_SERVER_USER`: 운영 서버 SSH 사용자
   - `PROD_SERVER_SSH_KEY`: 운영 서버 SSH 키

---

## 5️⃣ 전체 워크플로우 실습

### 완전한 시나리오: "회원가입 기능 추가 → 배포"

#### Phase 1: 개발 (개발자A)

```bash
# 1. 최신 develop 가져오기
git checkout develop
git pull origin develop

# 2. 작업 브랜치 생성
git checkout -b feature/signup

# 3. 코드 작성
echo "<?php\nfunction signup() { return 'Signup'; }\n?>" > signup.php

# 4. 커밋
git add .
git commit -m "feat: 회원가입 기능 추가"

# 5. 푸시
git push origin feature/signup
```

#### Phase 2: Pull Request & 리뷰

```bash
# 6. GitHub에서 PR 생성
# - Base: develop
# - Compare: feature/signup
# - 제목: "feat: 회원가입 기능 추가"

# 7. 개발자B가 코드 리뷰 후 Approve

# 8. PR 병합 (Squash and merge)
```

#### Phase 3: 자동 배포 (GitHub Actions)

```
✅ develop 브랜치에 병합됨
    ↓
🤖 GitHub Actions 자동 실행
    ↓
🐳 Docker 이미지 빌드
    ↓
📤 Docker Hub에 푸시
    ↓
🚀 개발 서버에 SSH 접속
    ↓
⬇️ 새 이미지 pull
    ↓
🔄 컨테이너 재시작
    ↓
✅ 개발 서버 배포 완료!
```

#### Phase 4: 운영 배포

```bash
# 9. develop → main PR 생성
# GitHub에서:
# - Base: main
# - Compare: develop
# - 제목: "Release v1.0.0"

# 10. 최종 승인 후 병합

# 11. GitHub Actions가 자동으로 운영 서버에 배포
```

#### Phase 5: 정리

```bash
# 12. 로컬 브랜치 정리
git checkout develop
git pull origin develop
git branch -d feature/signup
```

---

## 🎓 학습 체크리스트

### Git 기본
- [ ] 브랜치 생성 및 이동
- [ ] 커밋 메시지 작성
- [ ] 원격 저장소 푸시/풀

### GitHub 협업
- [ ] Pull Request 생성
- [ ] 코드 리뷰 작성
- [ ] PR 병합 및 브랜치 삭제

### 자동 배포
- [ ] GitHub Actions 워크플로우 작성
- [ ] Secrets 설정
- [ ] 배포 로그 확인

### 전체 흐름
- [ ] feature → develop → main 전체 워크플로우 실행
- [ ] 자동 배포 확인
- [ ] 팀원 간 협업 시뮬레이션

---

## 📚 추가 학습 자료

### 커밋 메시지 규칙 (Conventional Commits)
```
feat: 새로운 기능 추가
fix: 버그 수정
docs: 문서 수정
style: 코드 포맷팅 (세미콜론 등)
refactor: 코드 리팩토링
test: 테스트 코드 추가
chore: 빌드 업무, 패키지 매니저 수정
```

### GitHub Actions 트리거 종류
```yaml
on:
  push:           # 푸시할 때
  pull_request:   # PR 생성/업데이트 시
  schedule:       # 특정 시간에
  workflow_dispatch:  # 수동 실행
```

### 유용한 Git 명령어
```bash
# 브랜치 목록 확인
git branch -a

# 커밋 로그 확인
git log --oneline --graph

# 특정 파일 변경사항 확인
git diff filename

# 마지막 커밋 수정
git commit --amend

# 원격 브랜치 삭제
git push origin --delete branch-name

# 스테이징 취소
git reset HEAD filename

# 커밋 취소 (soft)
git reset --soft HEAD~1
```

---

## 🚨 자주 발생하는 문제

### 1. PR 충돌 (Conflict)
**원인:** 같은 파일을 여러 사람이 수정
**해결:**
```bash
git checkout feature/your-branch
git pull origin develop
# 충돌 해결
git add .
git commit -m "fix: merge conflict resolved"
git push origin feature/your-branch
```

### 2. 잘못된 브랜치에 커밋
```bash
# 커밋을 다른 브랜치로 이동
git checkout correct-branch
git cherry-pick <commit-hash>

# 원래 브랜치에서 커밋 제거
git checkout wrong-branch
git reset --hard HEAD~1
```

### 3. GitHub Actions 실패
1. `Actions` 탭에서 실패 로그 확인
2. Secrets 설정 확인
3. 워크플로우 YAML 문법 확인

---

## 📞 도움이 필요할 때

- GitHub 공식 문서: https://docs.github.com
- Git 공식 문서: https://git-scm.com/doc
- GitHub Actions 마켓플레이스: https://github.com/marketplace
