# 🚀 Git 협업 5분 퀵 스타트

## 📍 Pull Request가 뭔가요?

### 비유로 이해하기
```
당신이 회사에 보고서를 제출한다고 상상해보세요:

1. 보고서 작성 (코드 작성)
2. 상사에게 이메일 전송 (Push)
3. "검토 부탁드립니다" 메모 첨부 (Pull Request 생성) ← 이게 PR!
4. 상사가 검토 후 승인 (Code Review)
5. 최종 문서함에 보관 (Merge)
```

### Pull Request = "내 코드를 검토해주세요" 요청서

**왜 필요한가?**
- 코드 품질 유지 (다른 사람이 확인)
- 실수 방지 (버그 발견)
- 지식 공유 (팀원들이 변경사항 파악)

**매번 만드나요?**
- **예!** 작업 1개 = PR 1개
- 로그인 기능 추가 → PR 1개
- 버그 수정 → PR 1개
- 디자인 변경 → PR 1개

---

## ⚡ 30초 요약

```bash
# 1. 작업 시작
git checkout develop
git pull origin develop
git checkout -b feature/내작업

# 2. 코드 작성 후
git add .
git commit -m "feat: 기능 설명"
git push origin feature/내작업

# 3. GitHub 웹사이트에서
#    - "Compare & pull request" 버튼 클릭
#    - PR 작성 후 제출

# 4. 리뷰 후 병합되면
git checkout develop
git pull origin develop
git branch -d feature/내작업
```

---

## 🎯 실습 1: 첫 Pull Request 만들기 (5분)

### Step 1: 브랜치 생성
```bash
git checkout -b feature/my-first-pr
```

### Step 2: 파일 수정
```bash
echo "안녕하세요! 첫 PR입니다." > 설명.php
```

### Step 3: 커밋
```bash
git add 설명.php
git commit -m "docs: PR 테스트를 위한 파일 수정"
```

### Step 4: GitHub에 푸시
```bash
git push origin feature/my-first-pr
```

### Step 5: Pull Request 생성 (GitHub 웹사이트)

1. **GitHub 저장소 페이지로 이동**
   - https://github.com/leesangcheol/test

2. **노란색 배너 확인**
   ```
   📢 feature/my-first-pr had recent pushes 1 minute ago
   [Compare & pull request] 버튼
   ```

3. **버튼 클릭 후 PR 작성**
   ```
   제목: docs: PR 테스트를 위한 파일 수정

   설명:
   ## 변경 사항
   - 설명.php 파일 업데이트
   - Pull Request 테스트

   ## 체크리스트
   - [x] 파일 수정 완료
   - [x] 커밋 메시지 작성
   ```

4. **Create pull request 클릭**

### Step 6: (자기가) PR 승인 & 병합

1. **PR 페이지에서 `Merge pull request` 클릭**
2. **Confirm merge 클릭**
3. **Delete branch 클릭** (GitHub에서 브랜치 삭제)

### Step 7: 로컬 정리
```bash
git checkout master  # 또는 develop
git pull origin master
git branch -d feature/my-first-pr
```

**🎉 축하합니다! 첫 Pull Request 완료!**

---

## 🔄 실습 2: 협업 시뮬레이션 (2명 역할극)

### 📁 준비: 2개 폴더 사용

```
현재 폴더: D:\working_temp\github_test  (개발자A)
다른 폴더: D:\working_temp\github_test2 (개발자B)
```

### 👤 개발자A (현재 폴더)

```bash
# 1. 새 기능 개발
git checkout -b feature/add-login
echo "<?php function login() { return 'OK'; } ?>" > login.php

# 2. 커밋 & 푸시
git add .
git commit -m "feat: 로그인 기능 추가"
git push origin feature/add-login

# 3. GitHub에서 PR 생성
```

### 👤 개발자B (다른 폴더)

```bash
# 1. 다른 폴더로 이동
cd D:\working_temp\github_test2

# (처음이라면) 저장소 클론
git clone https://github.com/leesangcheol/test .

# 2. PR 브랜치 확인
git fetch origin
git checkout feature/add-login
cat login.php

# 3. GitHub에서 코드 리뷰 작성
#    - Files changed 탭
#    - Review changes → Approve

# 4. PR 병합
```

### 👤 개발자A (작업 마무리)

```bash
# 5. 병합된 코드 가져오기
git checkout master
git pull origin master
git branch -d feature/add-login

# 확인
cat login.php  # 개발자B가 병합한 내용 확인!
```

---

## 🤖 자동 배포는 어떻게 작동하나요?

### 마법의 흐름

```
1. develop 브랜치에 코드 병합 (PR merge)
         ↓
2. GitHub가 감지: "develop에 변화가 생겼네?"
         ↓
3. GitHub Actions 자동 실행
   - .github/workflows/deploy-dev.yml 파일 실행
         ↓
4. 다음 작업들을 자동으로 수행:
   a) Docker 이미지 빌드
   b) Docker Hub에 업로드
   c) 개발 서버에 SSH 접속
   d) 새 이미지 다운로드
   e) 컨테이너 재시작
         ↓
5. ✅ 개발 서버에 자동 배포 완료!
```

### 핵심: `.github/workflows/` 폴더

이 폴더에 YAML 파일을 만들면, GitHub가 자동으로 실행합니다.

**예시: `.github/workflows/deploy-dev.yml`**

```yaml
name: 개발 서버 자동 배포

# 언제 실행? develop 브랜치에 push될 때
on:
  push:
    branches:
      - develop

# 무엇을 실행?
jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: 코드 가져오기
        uses: actions/checkout@v3

      - name: Docker 빌드
        run: docker build -t myapp .

      - name: 서버에 배포
        run: |
          ssh user@server 'docker pull myapp && docker restart myapp'
```

---

## 🧪 실습 3: 자동 배포 테스트 (선택 사항)

### Dockerfile 준비

```bash
# 현재 폴더에 Dockerfile이 없다면
cat > Dockerfile << 'EOF'
FROM php:8.2-apache
COPY . /var/www/html/
EXPOSE 80
EOF
```

### GitHub Actions 워크플로우 추가

```bash
mkdir -p .github/workflows

cat > .github/workflows/hello.yml << 'EOF'
name: Hello GitHub Actions

on:
  push:
    branches:
      - master

jobs:
  hello:
    runs-on: ubuntu-latest
    steps:
      - name: Checkout code
        uses: actions/checkout@v3

      - name: Say hello
        run: echo "Hello! 자동화 성공!"

      - name: Show files
        run: ls -la
EOF
```

### 푸시 & 확인

```bash
git add .
git commit -m "ci: GitHub Actions 테스트"
git push origin master
```

**GitHub 웹사이트에서 확인:**
1. 저장소 페이지 → `Actions` 탭
2. 방금 실행된 워크플로우 확인
3. 로그 보기 → "Hello! 자동화 성공!" 메시지 확인

---

## 📊 전체 워크플로우 다이어그램

```
개발자A              GitHub              개발자B            서버
  |                    |                   |                |
  |--① 코드 작성------->|                   |                |
  |                    |                   |                |
  |--② Push----------->|                   |                |
  |                    |                   |                |
  |--③ PR 생성-------->|                   |                |
  |                    |                   |                |
  |                    |<--④ 코드 리뷰-----|                |
  |                    |                   |                |
  |                    |<--⑤ Approve------|                |
  |                    |                   |                |
  |                    |--⑥ Merge--------->|                |
  |                    |                   |                |
  |                    |--⑦ GitHub Actions---------------->|
  |                    |      (자동 배포)                  |
  |                    |                   |                |
  |<--⑧ Pull update----|                   |                |
  |                    |                   |                |
```

---

## 🎓 핵심 개념 복습

### Q1: Pull Request를 왜 만드나요?
**A:** 코드를 병합하기 전에 팀원들이 검토하도록 하기 위해서입니다.

### Q2: 매번 PR을 만들어야 하나요?
**A:** 예! 작업 하나당 PR 하나입니다. 작업이 끝날 때마다 새로 만듭니다.

### Q3: 자동 배포는 어떻게 되나요?
**A:** `.github/workflows/` 폴더의 YAML 파일이 GitHub Actions를 통해 자동 실행됩니다.

### Q4: develop과 main의 차이는?
**A:**
- `develop`: 개발 중인 코드, 개발 서버 배포용
- `main`: 안정화된 코드, 운영 서버 배포용

---

## 🚀 다음 단계

1. ✅ **실습 1 완료**: 첫 PR 만들기
2. ✅ **실습 2 완료**: 2명 역할극으로 협업 체험
3. ✅ **실습 3 완료**: GitHub Actions 테스트
4. 📖 **심화 학습**: GIT_WORKFLOW_GUIDE.md 참고

---

**💡 Tip:** 실습하면서 막히면 언제든지 물어보세요!
